<?php

namespace App\Services;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yansongda\LaravelPay\Facades\Pay;

class OrderRefundService
{
    /**
     * 管理员同意退款：清拒绝理由后按支付方式执行退款（支付宝走接口，微信预留）。
     */
    public function agreeRefund(Order $order): void
    {
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单状态不正确');
        }

        if (! $order->paid_at) {
            throw new InvalidRequestException('订单未支付，无法退款');
        }

        $extra = $order->extra ?: [];
        unset($extra['refund_disagree_reason'], $extra['refund_failed_code']);
        $order->update(['extra' => $extra]);

        $this->refundByPaymentMethod($order->fresh());
    }

    protected function refundByPaymentMethod(Order $order): void
    {
        switch ($order->payment_method) {
            case 'wechat':
                throw new InvalidRequestException('微信支付退款暂未实现');
            case 'alipay':
                $this->refundAlipay($order);
                break;
            default:
                throw new InternalException('未知订单支付方式：'.$order->payment_method);
        }
    }

    protected function refundAlipay(Order $order): void
    {
        $refundNo = Order::getAvailableRefundNo();

        try {
            $ret = Pay::alipay()->refund([
                'out_trade_no' => $order->no,
                'refund_amount' => (string) $order->total_amount,
                'out_request_no' => $refundNo,
            ]);
        } catch (Throwable $e) {
            Log::error('Alipay refund exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            $extra = $order->extra ?: [];
            $extra['refund_failed_code'] = $e->getMessage();

            $order->update([
                'refund_no' => $refundNo,
                'refund_status' => Order::REFUND_STATUS_FAILED,
                'extra' => $extra,
            ]);

            throw new InvalidRequestException('支付宝退款请求失败：'.$e->getMessage());
        }

        $failedDetail = $this->alipayRefundFailedDetail($ret);

        if ($failedDetail !== null) {
            $extra = $order->extra ?: [];
            $extra['refund_failed_code'] = $failedDetail;

            $order->update([
                'refund_no' => $refundNo,
                'refund_status' => Order::REFUND_STATUS_FAILED,
                'extra' => $extra,
            ]);

            return;
        }

        $extra = $order->extra ?: [];
        unset($extra['refund_failed_code']);

        $order->update([
            'refund_no' => $refundNo,
            'refund_status' => Order::REFUND_STATUS_SUCCESS,
            'extra' => $extra,
        ]);
    }

    /**
     * 解析支付宝退款结果：无失败信息返回 null，否则返回可展示的错误码/说明。
     */
    protected function alipayRefundFailedDetail(mixed $ret): ?string
    {
        if ($ret === null) {
            return 'empty_response';
        }

        $all = is_object($ret) && method_exists($ret, 'all')
            ? $ret->all()
            : (array) $ret;

        if (isset($all['alipay_trade_refund_response']) && is_array($all['alipay_trade_refund_response'])) {
            $r = $all['alipay_trade_refund_response'];
            if (! empty($r['sub_code'])) {
                return (string) $r['sub_code'];
            }
            if (($r['code'] ?? '') !== '10000') {
                return trim(($r['code'] ?? '').':'.($r['msg'] ?? 'error'));
            }

            return null;
        }

        if (! empty($all['sub_code'])) {
            return (string) $all['sub_code'];
        }

        if (isset($all['code']) && (string) $all['code'] !== '10000') {
            return (string) ($all['code'] ?? '').':'.($all['msg'] ?? '');
        }

        return null;
    }
}
