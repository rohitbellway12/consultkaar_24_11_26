@foreach($allergies as $key=>$allergy)
@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
$storagePath = ($appEnv == 'local') ? asset('/storage/allergy') : asset('/storage/app/public/allergy');
$allergy_fr =  \App\Models\Allergy::where(['aid' => "a".$allergy->id,'lang_code' => 'fr'])->first();
@endphp
    <tr>
        <td>{{$key+1}}</td>
        <td>{{$allergy->name}}</td> 
        <td>
            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$allergy->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.allergy.status',[$allergy['id'],$allergy->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$allergy->id}}" {{$allergy->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{route('admin.allergy.edit',[$allergy['id']])}}"title="{{__('messages.edit')}} {{__('messages.allergy')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$allergy['id']}}','Want to delete this allergy ?')" title="{{__('messages.delete')}} {{__('messages.allergy')}}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.allergy.delete',[$allergy['id']])}}"
                        method="post" id="{{$allergy['id']}}">
                    @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach