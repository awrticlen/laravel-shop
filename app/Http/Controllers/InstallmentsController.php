<?php
namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Events\OrderPaid;
use Carbon\Carbon;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Pay as YansongdaPay;
use Illuminate\Support\Str;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
        $installments = Installment::query()
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }
    public function show(Installment $installment)
    {
        // 取出当前分期付款的所有的还款计划，并按还款顺序排序
        $items = $installment->items()->orderBy('sequence')->get();
        $this->authorize('own', $installment);
        return view('installments.show', [
            'installment' => $installment,
            'items'       => $items,
            // 下一个未完成还款的还款计划
            'nextItem'    => $items->where('paid_at', null)->first(),
        ]);
    }
    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }
        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期订单已结清');
        }
        // 获取当前分期付款最近的一个未支付的还款计划
        if (!$nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()) {
            // 如果没有未支付的还款，原则上不可能，因为如果分期已结清则在上一个判断就退出了
            throw new InvalidRequestException('该分期订单已结清');
        }

        $config = config('pay');
        $config['alipay']['default']['notify_url'] = ngrok_url('installments.alipay.notify');
        $config['alipay']['default']['return_url'] = ngrok_url('installments.alipay.return');

        // 调用分期专用的支付宝网页支付，避免复用普通订单支付的全局回调地址
        $outTradeNo = $installment->no.'_'.$nextItem->sequence.'_'.Str::lower(Str::random(10));

        return YansongdaPay::alipay($config)->web([
            // 支付订单号使用分期流水号+还款计划编号
            'out_trade_no' => $outTradeNo,
            'total_amount' => $nextItem->total,
            'subject'      => '支付 Laravel Shop 的分期订单：'.$installment->no,
        ]);
    }
    // 支付宝前端回调
    public function alipayReturn()
    {
        try {
            $data = Pay::alipay()->callback();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        $this->syncInstallmentFromAlipay($data);

        $outTradeNo = (string) ($data->out_trade_no ?? '');
        $no = $outTradeNo !== '' ? explode('_', $outTradeNo, 2)[0] : '';

        if ($no !== '' && ($installment = Installment::query()->where('no', $no)->first())) {
            $base = rtrim((string) config('app.url'), '/');
            $target = $base.'/installments/'.$installment->getKey();

            return redirect()->away($target);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 支付宝后端回调
    public function alipayNotify()
    {
        // 校验支付宝回调参数是否正确
        $data = Pay::alipay()->callback();
        $this->syncInstallmentFromAlipay($data);

        return Pay::alipay()->success();
    }

    /**
     * 按支付宝回调结果同步本地分期付款状态。
     */
    protected function syncInstallmentFromAlipay(mixed $data): void
    {
        $tradeStatus = (string) ($data->trade_status ?? '');
        if ($tradeStatus !== '' && !in_array($tradeStatus, ['TRADE_SUCCESS', 'TRADE_FINISHED'], true)) {
            return;
        }

        $outTradeNo = (string) ($data->out_trade_no ?? '');
        if ($outTradeNo === '') {
            return;
        }

        // out_trade_no 格式：{installment_no}_{sequence}_{attempt_token}
        $parts = explode('_', $outTradeNo, 3);
        if (count($parts) < 2) {
            return;
        }

        $no = $parts[0];
        $sequence = (int) $parts[1];

        $installment = Installment::query()->where('no', $no)->first();
        if (! $installment) {
            return;
        }

        $item = $installment->items()->where('sequence', $sequence)->first();
        if (! $item) {
            return;
        }

        if ($item->paid_at) {
            return;
        }

        \DB::transaction(function () use ($data, $no, $installment, $item) {
            $item->update([
                'paid_at'        => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no'     => $data->trade_no,
            ]);

            if ($item->sequence === 0) {
                $installment->update(['status' => Installment::STATUS_REPAYING]);

                if (! $installment->order->paid_at) {
                    $installment->order->update([
                        'paid_at'        => Carbon::now(),
                        'payment_method' => 'installment',
                        'payment_no'     => $no,
                    ]);
                    event(new OrderPaid($installment->order));
                }
            }

            if ($item->sequence === $installment->count - 1) {
                $installment->update(['status' => Installment::STATUS_FINISHED]);
            }
        });
    }
}