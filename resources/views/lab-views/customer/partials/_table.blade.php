@foreach($customers as $k=>$e)
<tr>
    <th scope="row">{{$k+1}}</th>
    <td class="text-capitalize">{{$e['patent_name']}}</td>

    <td>{{$e['patient_mobile_number']}}</td>
</tr>
@endforeach