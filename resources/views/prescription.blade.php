<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="{{asset('public/css/custom.css')}}" rel="stylesheet">
<style type="text/css">
    body {
        width: 100% !important;
        font-size: 12px;
        font-family: "DejaVu Sans,Helvetica Neue", Helvetica, Arial, sans-serif!important;
    }

    * {
        font-family: "DejaVu Sans,Helvetica Neue", Helvetica, Arial, sans-serif!important;
    }

    .container {
        width: 700px;
        
    }

    .outer_border {
        border: 1px solid #999999!important;
        padding: 4%!important;
        margin-bottom: 2%!important;
        height:800px;
    }

    .top_box {
        width: 47%;
        padding: 0%;
    }

    .table_pad {
        padding: 0% 2%;
    }

    .border {
        border: 1px solid #CCCCCC!important;
    }

    .small_text {
        font-size: 10px!important;
    }

    .bg_color1 {
        background: #3a5082;
        color: #fff;
    }

    .text_color1 {
        color: #3a5082;
    }

    td {
        padding: 4px;
    }

    .pull-right {
        float: right;
    }
</style>
</head>
<body>
<div class="container">
<div class="outer_border">
    <div class="row">
        <div class="pull-left top_box p-4">
            <img src="{{ $logoPath }}" height="100">
            <br>
            <p>
                <?php
                $phoneData = \App\Models\BusinessSetting::where(['key' => 'phone'])->first();
                $emailData = \App\Models\BusinessSetting::where(['key' => 'email_address'])->first();
                ?>
                Phone: {{ ($phoneData) ? $phoneData->value : 'N/A' }} <br>
                Email: {{ ($emailData) ? $emailData->value : 'N/A' }} <br>
            </p>
        </div>
        <div class="pull-right top_box p-4" style="width:47%; padding:0%;top:-115px;position:absolute">
            <h2 style="color:#687cbf;font-weight: bold;font-size:30px; text-align:right; padding-right: 30px;margin-top:100px;" id="invoice">Prescription</h2>
            <table width="100%" height="70" border="0" class="table_pad" style="margin-top:20px;">
              
                <tr>
                    <td>Appointment Date</td>
                    <td>{{ date('d M Y ',strtotime($appointmentData['appoinmtent_date'])) }}</td>
                </tr>
                <tr>
                    <td width="50%">Prescription </td>
                    <td width="50%">#{{ $medicineData->id }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row" style="margin-top:130px;right:15px;position:relative">
   
                 <table width="100%" border="0">
                <tr>

                    <td colspan="2">
                        <div class="bg_color1" style="text-indent:10px;font-size: 14px;width: 50%;height: 26px;line-height: 18px; ">Patient Details - </div>
                        <table width="100%" border="0">
                            <tr>
                                <td>Name : {{ $appointmentData['patent_name'] }}</td>
                            </tr>
                            <tr>
                                <td>Mobile Number : {{ $appointmentData['patient_mobile_number'] }}</td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"> </td>
                </tr>
            </table>
     

    </div>


    <div class="row" style="right:15px;position:relative">
        <div class="bg_color1" style="text-indent:10px;font-size: 14px;width: 50%;height: 26px;line-height: 18px; ">Prescription Details - </div>
        <table height="82"  style="width:100%;margin-top:10px;" border="1" cellpadding="0" cellspacing="0">
            <tr class="bg_color1">
                <td width="18%">S. No.</td>
                <td width="58%" height="12" style="padding-left: 10px;">Medicine Name</td>
                <td width="13%">Quantity </td>
                <td width="58%" height="12" style="padding-left: 10px;">Dose (After Meal)</td>
                <td width="13%">Dose (Before Meal)</td>
            </tr>
            @php
                $count = 0;
            @endphp

            <?php
            $medicines = json_decode($medicineData->medicines,true);

            if(count($medicines) > 0){
                foreach ($medicines as $key => $detail){

                    

                    $count++;
                    ?>
                    <tr>
                        <td>{{$count }}</td>
                        <td> {{$detail['medicine_name'] ?? ''}}  </td>
                        <td>  {{ $detail['quantity'] ?? ''}}   </td>
                        <td> {{$detail['dose_before_meal'] }}  </td>
                        <td> {{$detail['dose_after_meal'] }}  </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        
        Note : {{ $medicineData->note }}
    </div>
  
</div>
</div>
