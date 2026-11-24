@foreach($diseases as $key=>$disease)
<tr>
    <td>{{$key+1}}</td>
    <td>
    <span class="d-block font-size-sm text-body">
        {{$disease['disease_name']}}
    </span>
    </td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{$disease['specialitiy_name']}}
        </span>
    </td>
    <?php
                
    $diseaseImagePath = (env('APP_ENV') == 'local') ? asset('storage/disease/' . $disease->disease_image) : asset('storage/app/public/disease/' . $disease->disease_image);        
   ?>
   <td>
    <img class="avatar avatar-lg mr-3" src="{{$diseaseImagePath}}" 
                 alt="{{$disease->name}} image">
         
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$disease->id}}">
        <input type="checkbox" onclick="location.href='{{route('admin.disease.status',[$disease['id'],$disease->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$disease->id}}" {{$disease->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white"
            href="{{route('admin.disease.edit',[$disease['id']])}}" title="{{__('messages.edit')}} {{__('messages.disease')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:"
        onclick="form_alert('disease-{{$disease['id']}}','Want to delete this disease ?')" title="{{__('messages.delete')}} {{__('messages.disease')}}"><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.disease.delete',[$disease['id']])}}" method="post" id="disease-{{$disease['id']}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
