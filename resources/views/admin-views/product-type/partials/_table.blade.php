@foreach($categories as $key=>$category)
<?php

    $category_en =  \App\Models\ProductType::where(['ptid' => "pt".$category->id,'lang_code' => 'en'])->first();
    $category_fr =  \App\Models\ProductType::where(['ptid' => "pt".$category->id,'lang_code' => 'fr'])->first();
?>
<tr>
    <td>{{$key+1}}</td>
    <td>{{$category_en->name ?? 'N/A'}}</td>    


    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
        <input type="checkbox" onclick="location.href='{{route('admin.product-type.status',[$category->id,$category->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white"
            href="{{route('admin.category.edit',[$category->id])}}" title="{{__('messages.edit')}} "><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:"
        onclick="form_alert('category-{{$category->id}}','Want to delete this category ?')" title="{{__('messages.delete')}} "><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.category.delete',[$category->id])}}" method="post" id="category-{{$category->id}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
