@foreach($surgeries as $key=>$surgery)
@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
    <tr>
        <td>{{$key+1}}</td>
        <td>{{$surgery->name}}</td> 
        <td>
            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$surgery->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.surgery.status',[$surgery['id'],$surgery->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$surgery->id}}" {{$surgery->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{route('admin.surgery.edit',[$surgery['id']])}}" title="{{__('messages.edit')}} surgery"><i class="tio-edit"></i></a>
            <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$surgery['id']}}','Want to delete this surgery ?')" title="{{__('messages.delete')}} surgery"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.surgery.delete',[$surgery['id']])}}"
                        method="post" id="{{$surgery['id']}}">
                    @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach