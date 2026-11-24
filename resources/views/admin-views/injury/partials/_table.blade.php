@foreach($injuries as $key=>$injury)

@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
    <tr>
        <?php
        $injury_en =  \App\Models\Injury::where(['iid' => "i".$injury['id'],'lang_code' => 'en'])->first();
         $injury_fr =  \App\Models\Injury::where(['iid' => "i".$injury['id'],'lang_code' => 'fr'])->first();
        ?>
        <td>{{$key+1}}</td>
        <td>{{$injury_en->name ?? 'N/A'}}</td>    
        <td>
            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$injury->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.injury.status',[$injury['id'],$injury->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$injury->id}}" {{$injury->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{route('admin.injury.edit',[$injury['id']])}}"title="{{__('messages.edit')}} injury"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$injury['id']}}','Want to delete this injury ?')" title="{{__('messages.delete')}} injury"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.injury.delete',[$injury['id']])}}"
                        method="post" id="{{$injury['id']}}">
                    @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach