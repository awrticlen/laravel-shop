@extends('layouts.app')
@section('title', '收货地址列表')

@section('content')
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>收货地址列表</span>
          <a href="{{ route('user_addresses.create') }}" class="btn btn-primary btn-sm">新增收货地址</a>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>收货人</th>
              <th>地址</th>
              <th>邮编</th>
              <th>电话</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->zip }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                <a href="{{ route('user_addresses.edit', ['user_address' => $address->id]) }}" 
                class="btn btn-primary">修改</a>
                <!-- 把之前删除按钮的表单替换成这个按钮，data-id 属性保存了这个地址的 id，在 js 里会用到 -->
<button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">删除</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scriptsAfterJs')
<script>
window.addEventListener('load', function() {
  if (typeof window.$ === 'undefined') return;
  $(document).ready(function() {
    $('.btn-del-address').click(function() {
      var id = $(this).data('id');
      swal({
        title: "确认要删除该地址？",
        icon: "warning",
        buttons: ['取消', '确定'],
        dangerMode: true,
      })
      .then(function(willDelete) {
        if (!willDelete) return;
        axios.delete('/user_addresses/' + id)
          .then(function () {
            location.reload();
          });
      });
    });
  });
});
</script>
@endsection