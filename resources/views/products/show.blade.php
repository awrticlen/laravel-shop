@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="products-index-page products-show-page">
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-5">
        <img class="cover" src="{{ $product->image_url ?: 'https://picsum.photos/seed/'.$product->id.'/400/400' }}" alt="{{ $product->title }}" onerror="this.src='https://picsum.photos/seed/{{ $product->id }}/400/400'">
      </div>
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group btn-group-toggle">
            @foreach($product->skus as $sku)
              <label
                class="btn sku-btn"
                data-price="{{ $sku->price }}"
                data-stock="{{ $sku->stock }}"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="{{ $sku->description }}">
                <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>数量</label><input type="text" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
        <div class="buttons">
          @if($favored)
            <button class="btn btn-danger btn-disfavor">取消收藏</button>
          @else
            <button class="btn btn-success btn-favor">❤ 收藏</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-bs-toggle="tab" aria-selected="true">商品详情</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-bs-toggle="tab" aria-selected="false">用户评价</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
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
      var priceSpan = document.querySelector('.product-info .price span');
      var stockSpan = document.querySelector('.product-info .stock');
      var skuBtns = document.querySelectorAll('.sku-btn');

      function updatePriceStock(label) {
        if (!label || !priceSpan || !stockSpan) return;
        var price = label.getAttribute('data-price');
        var stock = label.getAttribute('data-stock');
        if (price != null) priceSpan.textContent = price;
        if (stock != null) stockSpan.textContent = '库存：' + stock + '件';
      }

      // Bootstrap 5 工具提示
      if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
          new bootstrap.Tooltip(el, { trigger: 'hover' });
        });
      }
      // SKU 点击：更新价格与库存
      skuBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          updatePriceStock(this);
        });
      });
      // 初始：有选中则显示其价格/库存，否则选中第一个并显示
      var checkedLabel = document.querySelector('.sku-btn input:checked');
      if (checkedLabel) {
        checkedLabel = checkedLabel.closest('label');
        updatePriceStock(checkedLabel);
      } else {
        var firstBtn = skuBtns[0];
        if (firstBtn) {
          var firstInput = firstBtn.querySelector('input[name="skus"]');
          if (firstInput) {
            firstInput.checked = true;
            firstBtn.classList.add('active');
            updatePriceStock(firstBtn);
          }
        }
      }
      // 保持选中按钮的 active 样式，并同步价格/库存
      document.querySelectorAll('input[name="skus"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
          document.querySelectorAll('.btn-group-toggle .sku-btn').forEach(function (l) { l.classList.remove('active'); });
          var label = this.closest('label');
          if (label) label.classList.add('active');
          updatePriceStock(label);
        });
      });

      // 收藏按钮：发送 ajax 请求
      var favorBtn = document.querySelector('.btn-favor');
      if (favorBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        favorBtn.addEventListener('click', function () {
          axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
            .then(function () {
              swal('操作成功', '', 'success').then(function () {
                location.reload();
              });
            })
            .catch(function (error) {
              if (error.response && error.response.status === 401) {
                swal('请先登录', '', 'error');
              } else if (error.response && (error.response.data.msg || error.response.data.message)) {
                var msg = error.response.data.msg ? error.response.data.msg : error.response.data.message;
                swal(msg, '', 'error');
              } else {
                swal('系统错误', '', 'error');
              }
            });
        });
      }

      // 取消收藏按钮：发送 ajax 请求
      var disfavorBtn = document.querySelector('.btn-disfavor');
      if (disfavorBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        disfavorBtn.addEventListener('click', function () {
          axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
            .then(function () {
              swal('操作成功', '', 'success').then(function () {
                location.reload();
              });
            })
            .catch(function (error) {
              if (error.response && (error.response.data.msg || error.response.data.message)) {
                var msg = error.response.data.msg ? error.response.data.msg : error.response.data.message;
                swal(msg, '', 'error');
              } else {
                swal('系统错误', '', 'error');
              }
            });
        });
      }
    });
  </script>
@endsection