<!-- 如果当前类目有 children 字段并且 children 字段不为空 -->
@if(isset($category['children']) && count($category['children']) > 0)
  <li class="dropend category-item">
    <div class="category-item-row">
      <a href="{{ route('products.index', ['category_id' => $category['id']]) }}" class="dropdown-item category-item-link">{{ $category['name'] }}</a>
      <button class="category-item-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false" aria-label="展开{{ $category['name'] }}的子分类"></button>
      <ul class="dropdown-menu">
        <!-- 遍历当前类目的子类目，递归调用自己这个模板 -->
        @each('layouts._category_item', $category['children'], 'category')
      </ul>
    </div>
  </li>
@else
  <li><a class="dropdown-item" href="{{ route('products.index', ['category_id' => $category['id']]) }}">{{ $category['name'] }}</a></li>
@endif
