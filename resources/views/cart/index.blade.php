@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">我的购物车</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>商品信息</th>
        <th>单价</th>
        <th>数量</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach($cartItems as $item)
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <span class="sku_title">{{ $item->productSku->title }}</span>
              @if(!$item->productSku->product->on_sale)
                <span class="warning">该商品已下架</span>
              @endif
            </div>
          </td>
          <td><span class="price">￥{{ $item->productSku->price }}</span></td>
          <td>
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">移除</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div>
  <form role="form" id="order-form">
    <div class="row mb-3 align-items-center">
      <label class="col-form-label col-sm-3 text-md-end">选择收货地址</label>
      <div class="col-sm-9 col-md-7">
        <select class="form-select" name="address">
          @foreach($addresses as $address)
            <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="row mb-3 align-items-center">
      <label class="col-form-label col-sm-3 text-md-end">备注</label>
      <div class="col-sm-9 col-md-7">
        <textarea name="remark" class="form-control" rows="3"></textarea>
      </div>
    </div>
    <!-- 优惠码开始 -->
<div class="row mb-3 align-items-center">
  <label class="col-form-label col-sm-3 text-md-end">优惠码</label>
  <div class="col-sm-4">
    <input type="text" class="form-control" name="coupon_code">
    <span class="form-text text-muted" id="coupon_desc"></span>
  </div>
  <div class="col-sm-3">
    <button type="button" class="btn btn-success" id="btn-check-coupon">检查</button>
    <button type="button" class="btn btn-danger" style="display: none;" id="btn-cancel-coupon">取消</button>
  </div>
</div>
<!-- 优惠码结束 -->
    <div class="row mb-3">
      <div class="offset-sm-3 col-sm-3">
        <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
      </div>
    </div>
  </form>
</div>
</div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var removeButtons = document.querySelectorAll('.btn-remove');

      // 单个移除
      removeButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var tr = this.closest('tr');
          if (!tr) return;
          var id = tr.dataset.id;
          if (!id) return;

          swal({
            title: '确认要将该商品移除？',
            icon: 'warning',
            buttons: ['取消', '确定'],
            dangerMode: true,
          }).then(function (willDelete) {
            if (!willDelete) {
              return;
            }

            axios.delete('/cart/' + id)
              .then(function () {
                location.reload();
              })
              .catch(function (error) {
                if (error.response && error.response.status === 401) {
                  swal('请先登录', '', 'error');
                } else {
                  swal('系统错误', '', 'error');
                }
              });
          });
        });
      });

      // 全选 / 取消全选
      var selectAll = document.getElementById('select-all');
      if (selectAll) {
        selectAll.addEventListener('change', function () {
          var checked = this.checked;
          // 选择所有 name=select 且未 disabled 的复选框
          document
            .querySelectorAll('input[name="select"][type="checkbox"]:not(:disabled)')
            .forEach(function (checkbox) {
              checkbox.checked = checked;
            });
        });
      }

      // 创建订单
      var createOrderBtn = document.querySelector('.btn-create-order');
      if (createOrderBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        createOrderBtn.addEventListener('click', function () {
          var orderForm = document.getElementById('order-form');
          if (!orderForm) return;

          var addressSelect = orderForm.querySelector('select[name="address"]');
          var remarkTextarea = orderForm.querySelector('textarea[name="remark"]');

          var req = {
            address_id: addressSelect ? addressSelect.value : null,
            items: [],
            remark: remarkTextarea ? remarkTextarea.value : '',
          };

          // 遍历购物车中的每一行
          document.querySelectorAll('table tr[data-id]').forEach(function (tr) {
            var checkbox = tr.querySelector('input[name="select"][type="checkbox"]');
            if (!checkbox) return;
            // 跳过禁用或未选中的
            if (checkbox.disabled || !checkbox.checked) {
              return;
            }

            var amountInput = tr.querySelector('input[name="amount"]');
            if (!amountInput) return;
            var amount = amountInput.value;

            // 数量为 0 或非数字则跳过
            if (amount == 0 || isNaN(amount)) {
              return;
            }

            req.items.push({
              sku_id: tr.dataset.id,
              amount: amount,
            });
          });

          axios.post('{{ route('orders.store') }}', req)
            .then(function (response) {
              swal('订单提交成功', '', 'success')
  .then(() => {
    location.href = '/orders/' + response.data.id;
  });
            })
            .catch(function (error) {
              if (!error.response) {
                swal('系统错误', '', 'error');
                return;
              }

              if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                var errors = error.response.data.errors;
                var html = '';
                Object.keys(errors).forEach(function (field) {
                  errors[field].forEach(function (msg) {
                    html += msg + '<br>';
                  });
                });
                var div = document.createElement('div');
                div.innerHTML = html;
                swal({ content: div, icon: 'error' });
              } else {
                swal('系统错误', '', 'error');
              }
            });
        });
      }

      // 检查优惠码按钮点击事件（原生 JS）
      var checkCouponBtn = document.getElementById('btn-check-coupon');
      var cancelCouponBtn = document.getElementById('btn-cancel-coupon');
      var couponInput = document.querySelector('input[name="coupon_code"]');
      var couponDesc = document.getElementById('coupon_desc');

      if (
        checkCouponBtn &&
        cancelCouponBtn &&
        couponInput &&
        couponDesc &&
        typeof axios !== 'undefined' &&
        typeof swal !== 'undefined'
      ) {
        checkCouponBtn.addEventListener('click', function () {
          var code = (couponInput.value || '').trim();

          if (!code) {
            swal('请输入优惠码', '', 'warning');
            return;
          }

          axios
            .get('/coupon_codes/' + encodeURIComponent(code))
            .then(function (response) {
              couponDesc.textContent = response.data.description || '';
              couponInput.readOnly = true; // 禁用输入框（保留值方便订单提交）
              cancelCouponBtn.style.display = ''; // 显示取消按钮
              checkCouponBtn.style.display = 'none'; // 隐藏检查按钮
            })
            .catch(function (error) {
              var status = error && error.response ? error.response.status : null;

              if (status === 404) {
                swal('优惠码不存在', '', 'error');
              } else if (status === 403) {
                var msg =
                  error.response && error.response.data && error.response.data.msg
                    ? error.response.data.msg
                    : '请求被拒绝';
                swal(msg, '', 'error');
              } else if (status === 401) {
                swal('请先登录', '', 'error');
              } else {
                swal('系统内部错误', '', 'error');
              }
            });
        });

        cancelCouponBtn.addEventListener('click', function () {
          couponDesc.textContent = '';
          couponInput.readOnly = false;
          cancelCouponBtn.style.display = 'none';
          checkCouponBtn.style.display = '';
        });
      }
    });
  </script>
@endsection