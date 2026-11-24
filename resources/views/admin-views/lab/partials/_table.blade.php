@foreach($labs as $key=>$lab)

<?php

$lab_en =  \App\Models\Lab::where(['lid' => "l".$lab->id,'lang_code' => 'en'])->first();
 $lab_fr =  \App\Models\Lab::where(['lid' => "l".$lab->id,'lang_code' => 'fr'])->first();
?>
<tr>
    <td>{{$key+1}}</td>
    <td>{{$lab_en->name ?? 'N/A'}}</td>    
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$lab->id}}">
        <input type="checkbox" onclick="location.href='{{route('admin.lab.status',[$lab->id,$lab->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$lab->id}}" {{$lab->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white"
            href="{{route('admin.lab.edit',[$lab->id])}}" title="{{__('messages.edit')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:"
        onclick="form_alert('lab-{{$lab->id}}','Want to delete this lab ?')" title="{{__('messages.delete')}} "><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.lab.delete',[$lab->id])}}" method="post" id="lab-{{$lab->id}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
