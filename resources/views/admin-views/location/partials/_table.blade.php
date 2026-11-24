@foreach($locations as $key=>$location)
@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
$storagePath = ($appEnv == 'local') ? asset('/storage/location') : asset('/storage/app/public/location');
@endphp
    <tr>
        <td>{{$key+1}}</td>
        <td>{{$location->name}}</td> 
        <td>
            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$location->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.location.status',[$location['id'],$location->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$location->id}}" {{$location->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{route('admin.location.edit',[$location['id']])}}" title="{{__('messages.edit')}} {{__('messages.location')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$location['id']}}','Want to delete this location ?')" title="{{__('messages.delete')}} {{__('messages.location')}}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.location.delete',[$location['id']])}}"
                        method="post" id="{{$location['id']}}">
                    @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach