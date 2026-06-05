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
                <img class="cover" src="{{ $product->image_url ?: 'https://picsum.photos/seed/' . $product->id . '/400/400' }}"
                  alt="{{ $product->title }}" onerror="this.src='https://picsum.photos/seed/{{ $product->id }}/400/400'">
              </div>
              <div class="col-7">
                <div class="title">{{ $product->long_title ?: $product->title }}</div>
                @if ($product->type === \App\Models\Product::TYPE_CROWDFUNDING)
                  <div class="crowdfunding-info">
                    <div class="have-text">已筹到</div>
                    <div class="total-amount"><span class="symbol">￥</span>{{ $product->crowdfunding->total_amount }}
                    </div>
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped" role="progressbar"
                        aria-valuenow="{{ $product->crowdfunding->percent }}" aria-valuemin="0" aria-valuemax="100"
                        style="min-width: 1em; width: {{ min($product->crowdfunding->percent, 100) }}%">
                      </div>
                    </div>
                    <div class="progress-info">
                      <span class="current-progress">当前进度：{{ $product->crowdfunding->percent }}%</span>
                      <span class="float-end user-count">{{ $product->crowdfunding->user_count }}名支持者</span>
                    </div>
                    @if ($product->crowdfunding->status === \App\Models\CrowdfundingProduct::STATUS_FUNDING)
                      <div>
                        此项目必须在
                        <span class="text-danger">{{ $product->crowdfunding->end_at->format('Y-m-d H:i:s') }}</span>
                        前得到
                        <span class="text-danger">￥{{ $product->crowdfunding->target_amount }}</span>
                        的支持才可成功，筹款将在
                        <span class="text-danger">{{ $product->crowdfunding->end_at->diffForHumans(now()) }}</span>
                        结束！
                      </div>
                    @endif
                  </div>
                @else
                  <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
                  <div class="sales_and_reviews">
                    <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
                    <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
                    <div class="rating" title="评分 {{ $product->rating }}">评分 <span
                        class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span>
                    </div>
                  </div>
                @endif
                <div class="skus">
                  <label>选择</label>
                  <div class="btn-group" role="group" aria-label="选择 SKU">
                    @foreach ($product->skus as $sku)
                      <label class="btn sku-btn" data-price="{{ $sku->price }}" data-stock="{{ $sku->stock }}"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $sku->description }}">
                        <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}">
                        {{ $sku->title }}
                      </label>
                    @endforeach
                  </div>
                </div>
                <div class="cart_amount"><label>数量</label><input type="text" class="form-control form-control-sm"
                    value="1"><span>件</span><span class="stock"></span></div>
                <div class="buttons">
                  @if ($favored)
                    <button class="btn btn-danger btn-disfavor">取消收藏</button>
                  @else
                    <button class="btn btn-success btn-favor">❤ 收藏</button>
                  @endif
                  @if ($product->type === \App\Models\Product::TYPE_CROWDFUNDING)
                    @auth
                      @if ($product->crowdfunding->status === \App\Models\CrowdfundingProduct::STATUS_FUNDING)
                        <button class="btn btn-primary btn-crowdfunding">参与众筹</button>
                      @else
                        <button class="btn btn-primary" disabled>
                          {{ \App\Models\CrowdfundingProduct::$statusMap[$product->crowdfunding->status] }}
                        </button>
                      @endif
                    @else
                      <a class="btn btn-primary" href="{{ route('login') }}">请先登录</a>
                    @endauth
                  @elseif ($product->type === \App\Models\Product::TYPE_SECKILL)
                    @auth
                      @if ($product->seckill->is_before_start)
                        <button class="btn btn-primary btn-seckill disabled countdown">抢购倒计时</button>
                      @elseif ($product->seckill->is_after_end)
                        <button class="btn btn-primary btn-seckill disabled">抢购已结束</button>
                      @else
                        <button class="btn btn-primary btn-seckill">立即抢购</button>
                      @endif
                    @else
                      <a class="btn btn-primary" href="{{ route('login') }}">请先登录</a>
                    @endauth
                  @else
                    <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                  @endif
                </div>
              </div>
            </div>
            <div class="product-detail">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="product-detail-tab-link" href="#product-detail-tab" data-bs-toggle="tab"
                    data-bs-target="#product-detail-tab" role="tab" aria-controls="product-detail-tab"
                    aria-selected="true">商品详情</a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link" id="product-reviews-tab-link" href="#product-reviews-tab" data-bs-toggle="tab"
                    data-bs-target="#product-reviews-tab" role="tab" aria-controls="product-reviews-tab"
                    aria-selected="false">用户评价</a>
                </li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="product-detail-tab"
                  aria-labelledby="product-detail-tab-link">
                  <!-- 产品属性开始 -->
                  <div class="properties-list">
                    <div class="properties-list-title">产品参数：</div>
                    <ul class="properties-list-body">
                      @foreach ($product->grouped_properties as $name => $values)
                        <li>{{ $name }}：{{ join(' ', $values) }}</li>
                      @endforeach
                    </ul>
                  </div>
                  <!-- 产品属性结束 -->
                  <div class="product-description">
                    {!! $product->description !!}
                  </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="product-reviews-tab"
                  aria-labelledby="product-reviews-tab-link">
                  <!-- 评论列表开始 -->
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th scope="col">用户</th>
                        <th scope="col">商品</th>
                        <th scope="col">评分</th>
                        <th scope="col">评价</th>
                        <th scope="col">时间</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($reviews as $review)
                        <tr>
                          <td>{{ $review->order->user->name }}</td>
                          <td>{{ $review->productSku->title }}</td>
                          <td>{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</td>
                          <td>{{ $review->review }}</td>
                          <td>{{ $review->reviewed_at->format('Y-m-d H:i') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <!-- 评论列表结束 -->
                </div>
              </div>
            </div>
            <!-- 猜你喜欢开始 -->
            @if (count($similar) > 0)
              <div class="similar-products">
                <div class="title">猜你喜欢</div>
                <div class="row products-list">
                  <!-- 这里不能使用 $product 作为 foreach 出来的变量，否则会覆盖掉当前页面的 $product 变量 -->
                  @foreach ($similar as $p)
                    <div class="col-3 product-item">
                      <div class="product-content">
                        <div class="top">
                          <div class="img">
                            <a href="{{ route('products.show', ['product' => $p->id]) }}">
                              <img src="{{ $p->image_url }}" alt="">
                            </a>
                          </div>
                          <div class="price"><b>￥</b>{{ $p->price }}</div>
                          <div class="title">
                            <a href="{{ route('products.show', ['product' => $p->id]) }}">{{ $p->title }}</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
            <!-- 猜你喜欢结束 -->
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scriptsAfterJs')
  @if ($product->type === \App\Models\Product::TYPE_SECKILL && $product->seckill->is_before_start)
    <script>
      window.__seckillStartAt = {{ $product->seckill->start_at->getTimestamp() }};
    </script>
  @endif
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var isCrowdfunding = @json($product->type === \App\Models\Product::TYPE_CROWDFUNDING);
      var isSeckill = @json($product->type === \App\Models\Product::TYPE_SECKILL);
      var seckillBeforeStart = @json($product->type === \App\Models\Product::TYPE_SECKILL && $product->seckill->is_before_start);
      var addresses = @json(Auth::check() ? Auth::user()->addresses : []);
      var priceSpan = document.querySelector('.product-info .price span');
      var stockSpan = document.querySelector('.product-info .stock');
      var skuBtns = document.querySelectorAll('.sku-btn');

      function updatePriceStock(label) {
        if (!label) return;
        var price = label.getAttribute('data-price');
        var stock = label.getAttribute('data-stock');
        if (price != null && priceSpan) priceSpan.textContent = price;
        if (stock != null && stockSpan) stockSpan.textContent = '库存：' + stock + '件';
      }

      // Bootstrap 5 工具提示
      if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
          new bootstrap.Tooltip(el, {
            trigger: 'hover'
          });
        });
      }
      // SKU 点击：更新价格与库存
      skuBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
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
      document.querySelectorAll('input[name="skus"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
          document.querySelectorAll('.btn-group .sku-btn').forEach(function(l) {
            l.classList.remove('active');
          });
          var label = this.closest('label');
          if (label) label.classList.add('active');
          updatePriceStock(label);
        });
      });

      // 收藏按钮：发送 ajax 请求
      var favorBtn = document.querySelector('.btn-favor');
      if (favorBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        favorBtn.addEventListener('click', function() {
          axios.post('{{ route('products.favor', ['product' => $product->id]) }}')
            .then(function() {
              swal('操作成功', '', 'success').then(function() {
                location.reload();
              });
            })
            .catch(function(error) {
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
        disfavorBtn.addEventListener('click', function() {
          axios.delete('{{ route('products.disfavor', ['product' => $product->id]) }}')
            .then(function() {
              swal('操作成功', '', 'success').then(function() {
                location.reload();
              });
            })
            .catch(function(error) {
              if (error.response && (error.response.data.msg || error.response.data.message)) {
                var msg = error.response.data.msg ? error.response.data.msg : error.response.data.message;
                swal(msg, '', 'error');
              } else {
                swal('系统错误', '', 'error');
              }
            });
        });
      }

      // 加入购物车按钮：发送 ajax 请求
      var addToCartBtn = document.querySelector('.btn-add-to-cart');
      if (addToCartBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        addToCartBtn.addEventListener('click', function() {
          // 当前选中的 SKU
          var checkedSku = document.querySelector('input[name="skus"]:checked');
          if (!checkedSku) {
            swal('请选择商品 SKU', '', 'error');
            return;
          }

          // 购买数量
          var amountInput = document.querySelector('.cart_amount input');
          var amount = amountInput ? amountInput.value : 1;

          axios.post('{{ route('cart.add') }}', {
              sku_id: checkedSku.value,
              amount: amount,
            })
            .then(function() {
              swal('加入购物车成功', '', 'success').then(function() {
                location.href = '{{ route('cart.index') }}';
              });
            })
            .catch(function(error) {
              if (!error.response) {
                swal('系统错误', '', 'error');
                return;
              }

              // 未登录
              if (error.response.status === 401) {
                swal('请先登录', '', 'error');
                return;
              }

              // 参数校验失败
              if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                var errors = error.response.data.errors;
                var html = '';
                Object.keys(errors).forEach(function(field) {
                  errors[field].forEach(function(msg) {
                    html += msg + '<br>';
                  });
                });
                var div = document.createElement('div');
                div.innerHTML = html;
                swal({
                  content: div,
                  icon: 'error'
                });
                return;
              }

              // 其他错误，尽量把后端消息展示出来
              var msg = error.response.data && (error.response.data.msg || error.response.data.message);
              swal(msg || '系统错误', '', 'error');
            });
        });
      }

      // 众筹下单：原生 JS + SweetAlert
      var crowdfundingBtn = document.querySelector('.btn-crowdfunding');
      if (isCrowdfunding && crowdfundingBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        crowdfundingBtn.addEventListener('click', function() {
          var checkedSku = document.querySelector('input[name="skus"]:checked');
          if (!checkedSku) {
            swal('请先选择商品', '', 'warning');
            return;
          }
          if (!Array.isArray(addresses) || addresses.length === 0) {
            swal('请先添加收货地址', '', 'warning');
            return;
          }

          var wrapper = document.createElement('div');
          wrapper.innerHTML =
            '<div class="mb-3">' +
            '  <label class="form-label">选择地址</label>' +
            '  <select class="form-select" name="address_id"></select>' +
            '</div>' +
            '<div class="mb-1">' +
            '  <label class="form-label">购买数量</label>' +
            '  <input class="form-control" name="amount" type="number" min="1" value="1">' +
            '</div>';

          var addressSelect = wrapper.querySelector('select[name="address_id"]');
          addresses.forEach(function(address) {
            var option = document.createElement('option');
            option.value = address.id;
            option.textContent = address.full_address + ' ' + address.contact_name + ' ' + address
              .contact_phone;
            addressSelect.appendChild(option);
          });

          swal({
            text: '参与众筹',
            content: wrapper,
            buttons: ['取消', '确定'],
          }).then(function(confirmed) {
            if (!confirmed) return;

            var amountInput = wrapper.querySelector('input[name="amount"]');
            var amount = amountInput ? parseInt(amountInput.value, 10) : 1;
            if (!amount || amount < 1) {
              swal('购买数量必须大于 0', '', 'warning');
              return;
            }

            var req = {
              address_id: addressSelect ? addressSelect.value : null,
              amount: amount,
              sku_id: checkedSku.value,
            };

            axios.post('{{ route('crowdfunding_orders.store') }}', req)
              .then(function(response) {
                swal('订单提交成功', '', 'success').then(function() {
                  location.href = '/orders/' + response.data.id;
                });
              })
              .catch(function(error) {
                if (!error.response) {
                  swal('系统错误', '', 'error');
                  return;
                }
                if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                  var errors = error.response.data.errors;
                  var html = '';
                  Object.keys(errors).forEach(function(field) {
                    errors[field].forEach(function(msg) {
                      html += msg + '<br>';
                    });
                  });
                  var div = document.createElement('div');
                  div.innerHTML = html;
                  swal({
                    content: div,
                    icon: 'error'
                  });
                } else if (error.response.status === 403) {
                  var msg = error.response.data && (error.response.data.msg || error.response.data.message);
                  swal(msg || '请求被拒绝', '', 'error');
                } else {
                  swal('系统错误', '', 'error');
                }
              });
          });
        });
      }

      // 秒杀倒计时（原生 Date，无需 moment.js）
      if (isSeckill && seckillBeforeStart && typeof window.__seckillStartAt === 'number') {
        var seckillBtn = document.querySelector('.btn-seckill');
        var startTimeMs = window.__seckillStartAt * 1000;

        function padTwo(n) {
          return String(n).padStart(2, '0');
        }

        var countdownTimer = setInterval(function() {
          var now = Date.now();
          if (now >= startTimeMs) {
            if (seckillBtn) {
              seckillBtn.classList.remove('disabled', 'countdown');
              seckillBtn.textContent = '立即抢购';
            }
            clearInterval(countdownTimer);
            return;
          }

          var diffSec = Math.floor((startTimeMs - now) / 1000);
          var hourDiff = Math.floor(diffSec / 3600);
          var minDiff = Math.floor((diffSec % 3600) / 60);
          var secDiff = diffSec % 60;
          if (seckillBtn) {
            seckillBtn.textContent = '抢购倒计时 ' + padTwo(hourDiff) + ':' + padTwo(minDiff) + ':' + padTwo(secDiff);
          }
        }, 500);
      }

      // 秒杀下单
      var seckillBtn = document.querySelector('.btn-seckill');
      if (isSeckill && seckillBtn && typeof axios !== 'undefined' && typeof swal !== 'undefined') {
        seckillBtn.addEventListener('click', function() {
          if (this.classList.contains('disabled')) {
            return;
          }

          var checkedSku = document.querySelector('input[name="skus"]:checked');
          if (!checkedSku) {
            swal('请先选择商品', '', 'warning');
            return;
          }

          if (!Array.isArray(addresses) || addresses.length === 0) {
            swal('请先添加收货地址', '', 'warning');
            return;
          }

          var wrapper = document.createElement('div');
          wrapper.innerHTML =
            '<div class="mb-3">' +
            '  <label class="form-label">选择收货地址</label>' +
            '  <select class="form-select" name="address_id"></select>' +
            '</div>';

          var addressSelect = wrapper.querySelector('select[name="address_id"]');
          addresses.forEach(function(address) {
            var option = document.createElement('option');
            option.value = address.id;
            option.textContent = address.full_address + ' ' + address.contact_name + ' ' + address.contact_phone;
            addressSelect.appendChild(option);
          });

          swal({
            text: '选择收货地址',
            content: wrapper,
            buttons: ['取消', '确定'],
          }).then(function(confirmed) {
            if (!confirmed) {
              return;
            }

            var addressId = addressSelect ? parseInt(addressSelect.value, 10) : NaN;
            var address = null;
            for (var i = 0; i < addresses.length; i++) {
              if (addresses[i].id === addressId) {
                address = addresses[i];
                break;
              }
            }
            if (!address) {
              swal('请选择收货地址', '', 'warning');
              return;
            }

            var req = {
              address: {
                province: address.province,
                city: address.city,
                district: address.district,
                address: address.address,
                zip: address.zip,
                contact_name: address.contact_name,
                contact_phone: address.contact_phone,
              },
              sku_id: checkedSku.value,
            };

            axios.post('{{ route('seckill_orders.store') }}', req)
              .then(function(response) {
                swal('订单提交成功', '', 'success').then(function() {
                  location.href = '/orders/' + response.data.id;
                });
              })
              .catch(function(error) {
                if (!error.response) {
                  swal('系统错误', '', 'error');
                  return;
                }
                if (error.response.status === 422 && error.response.data && error.response.data.errors) {
                  var errors = error.response.data.errors;
                  var html = '';
                  Object.keys(errors).forEach(function(field) {
                    errors[field].forEach(function(msg) {
                      html += msg + '<br>';
                    });
                  });
                  var div = document.createElement('div');
                  div.innerHTML = html;
                  swal({
                    content: div,
                    icon: 'error'
                  });
                } else if (error.response.status === 403) {
                  var msg = error.response.data && (error.response.data.msg || error.response.data.message);
                  swal(msg || '请求被拒绝', '', 'error');
                } else {
                  swal('系统错误', '', 'error');
                }
              });
          });
        });
      }
    });
  </script>
@endsection
