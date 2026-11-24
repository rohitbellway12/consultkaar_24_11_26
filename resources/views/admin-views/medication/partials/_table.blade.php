@foreach($medications as $key=>$medication)
@php
$appEnv = env('APP_ENV');
$assetPrefixPath = ($appEnv == 'local') ? '' : 'public';
@endphp
    <tr>
        <?php
        $medication_en =  \App\Models\Medication::where(['mid' => "m".$medication->id,'lang_code' => 'en'])->first();
         $medication_fr =  \App\Models\Medication::where(['mid' => "m".$medication->id,'lang_code' => 'fr'])->first();
        ?>
        <td>{{$key+1}}</td>
        <td>{{$medication_en->name ?? 'N/A'}}</td>    
        <td>
            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$medication->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.medication.status',[$medication['id'],$medication->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$medication->id}}" {{$medication->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{route('admin.medication.edit',[$medication['id']])}}"title="{{__('messages.edit')}} medication"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('{{$medication['id']}}','Want to delete this medication ?')" title="{{__('messages.delete')}} medication"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.medication.delete',[$medication['id']])}}"
                        method="post" id="{{$medication['id']}}">
                    @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach