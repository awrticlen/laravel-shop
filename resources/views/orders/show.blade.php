@extends('layouts.app')
@section('title', '查看订单')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h4>订单详情</h4>
  </div>
  <div class="card-body">
    <table class="table">
      <thead>
      <tr>
        <th>商品信息</th>
        <th class="text-center">单价</th>
        <th class="text-center">数量</th>
        <th class="text-end item-amount">小计</th>
      </tr>
      </thead>
      @foreach($order->items as $index => $item)
        <tr>
          <td class="product-info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                <img src="{{ $item->product->image_url }}">
              </a>
            </div>
            <div>
              <span class="product-title">
                 <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
              </span>
              <span class="sku-title">{{ $item->productSku->title }}</span>
            </div>
          </td>
          <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
          <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
          <td class="item-amount text-end vertical-middle">￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
        </tr>
      @endforeach
      <tr><td colspan="4"></td></tr>
    </table>
    <div class="order-bottom">
      <div class="order-info">
        <div class="line"><div class="line-label">收货地址：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
        <div class="line"><div class="line-label">订单备注：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
        <div class="line"><div class="line-label">订单编号：</div><div class="line-value">{{ $order->no }}</div></div>
        <!-- 输出物流状态 -->
        <div class="line">
          <div class="line-label">物流状态：</div>
          <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
        </div>
        <!-- 如果有物流信息则展示 -->
        @if($order->ship_data)
        <div class="line">
          <div class="line-label">物流信息：</div>
          <div class="line-value">
            {{
              [
                'SF' => '顺丰',
                'YTO' => '圆通',
                'ZTO' => '中通',
                'YD' => '韵达',
                'STO' => '申通',
              ][$order->ship_data['express_company'] ?? '']
              ?? ($order->ship_data['express_company'] ?? '-')
            }}
            {{ $order->ship_data['express_no'] ?? '-' }}
          </div>
        </div>
        @endif
        <!-- 订单已支付，且退款状态不是未退款时展示退款信息 -->
        @if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
        <div class="line">
          <div class="line-label">退款状态：</div>
          <div class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</div>
        </div>
        <div class="line">
          <div class="line-label">退款理由：</div>
          <div class="line-value">{{ $order->extra['refund_reason'] ?? '-' }}</div>
        </div>
        @endif
      </div>
      <div class="order-summary text-end">
        <!-- 展示优惠信息开始 -->
        @if($order->couponCode)
        <div class="text-primary">
          <span>优惠信息：</span>
          <div class="value">{{ $order->couponCode->description }}</div>
        </div>
        @endif
        <!-- 展示优惠信息结束 -->
        <div class="total-amount">
          <span>订单总价：</span>
          <div class="value">￥{{ $order->total_amount }}</div>
        </div>
        <div>
          <span>订单状态：</span>
          <div class="value">
            @if($order->paid_at)
              已支付
            @elseif($order->closed)
              已关闭
            @else
              未支付
            @endif
          </div>
          </div>
        @if(isset($order->extra['refund_disagree_reason']))
        <div>
          <span>拒绝退款理由：</span>
          <div class="value">{{ $order->extra['refund_disagree_reason'] }}</div>
        </div>
        @endif
          <!-- 如果订单的发货状态为已发货则展示确认收货按钮 -->
          @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
          <div class="receive-button">
            <button type="button" id="btn-receive" class="btn btn-sm btn-success">确认收货</button>
          </div>
          @endif
        <!-- 非众筹订单、已支付，且未申请退款或退款失败时展示申请退款按钮（支持二次申请） -->
        @if(
          $order->type !== \App\Models\Order::TYPE_CROWDFUNDING &&
          $order->paid_at &&
          in_array($order->refund_status, [
            \App\Models\Order::REFUND_STATUS_PENDING,
            \App\Models\Order::REFUND_STATUS_FAILED,
          ], true)
        )
        <div class="refund-button">
          <button type="button" class="btn btn-sm btn-danger" id="btn-apply-refund">申请退款</button>
        </div>
        @endif
        <!-- 支付按钮开始 -->
        @if(!$order->paid_at && !$order->closed)
        <div class="payment-buttons">
          <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
        </div>
        @endif
        <!-- 支付按钮结束 -->
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('btn-receive');

  const submitReceive = async function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const url = @json(route('orders.received', [$order->id]));

    try {
      const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? ''
        },
        body: new URLSearchParams()
      });

      if (res.ok) {
        location.reload();
        return;
      }

      alert('确认收货失败，请稍后重试');
    } catch (e) {
      alert('网络异常，请稍后重试');
    }
  };

  if (btn) {
    btn.addEventListener('click', function () {
      // 使用项目已引入的 sweetalert（教程图二风格）
      if (typeof window.swal === 'function') {
        window.swal({
          title: '确认已经收到商品？',
          icon: 'warning',
          dangerMode: true,
          buttons: ['取消', '确认收到'],
        }).then(function (ret) {
          if (!ret) return;
          submitReceive();
        });

        return;
      }

      // 兜底：sweetalert 未加载时使用原生 confirm
      const ok = window.confirm('确认已经收到商品？');
      if (!ok) return;
      submitReceive();
    });
  }

  const refundBtn = document.getElementById('btn-apply-refund');
  if (refundBtn) {
    refundBtn.addEventListener('click', function () {
      if (typeof window.swal === 'function') {
        window.swal({
          text: '请输入退款理由',
          content: 'input',
        }).then(async function (input) {
          if (!input) {
            window.swal('退款理由不可空', '', 'error');
            return;
          }

          try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = @json(route('orders.apply_refund', [$order->id]));
            const res = await fetch(url, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken ?? '',
              },
              body: JSON.stringify({ reason: input }),
            });

            if (!res.ok) {
              window.swal('申请退款失败', '', 'error');
              return;
            }

            window.swal('申请退款成功', '', 'success').then(function () {
              location.reload();
            });
          } catch (e) {
            window.swal('网络异常，请稍后重试', '', 'error');
          }
        });

        return;
      }

      const input = window.prompt('请输入退款理由');
      if (!input) {
        alert('退款理由不可空');
        return;
      }

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const url = @json(route('orders.apply_refund', [$order->id]));

      fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken ?? '',
        },
        body: JSON.stringify({ reason: input }),
      }).then(function (res) {
        if (!res.ok) {
          alert('申请退款失败');
          return;
        }

        alert('申请退款成功');
        location.reload();
      }).catch(function () {
        alert('网络异常，请稍后重试');
      });
    });
  }
});
</script>
@endsection