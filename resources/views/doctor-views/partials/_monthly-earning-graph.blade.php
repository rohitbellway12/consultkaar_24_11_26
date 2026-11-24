<div class="card-body">
    <div class="row mb-4">
        <div class="col-sm mb-2 mb-sm-0">
            @php($params=session('dash_params'))
          
            <div class="d-flex align-items-center">
                <span class="h3 mb-0">
                    <span class="legend-indicator bg-primary" style="background-color: #511281!important;"></span>
                   Total Earnings : {{\App\CentralLogics\Helpers::format_currency(array_sum($total_sell))}}
                     
                </span>
            </div>
            
        </div>

        <div class="col-sm-auto align-self-sm-end">
            <!-- Legend Indicators -->
            <div class="row font-size-sm">
                <div class="col-auto">
                    <h5 class="card-header-title"><i class="tio-chart-bar-4" style="font-size: 50px"></i></h5>
                    
                </div>
                <div class="col-auto">
                    <select class="form-control d-none" name="year" id="year">
                        <option value="">Select year</option>
                        <?php
                        $currentYear = date("Y");
                        for ($i = $currentYear; $i <= $currentYear + 20; $i++) {
                            echo '<option value="' . $i . '"';
                            if ($i === $currentYear) {
                                echo ' selected';
                            }
                            echo '>' . $i . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <!-- End Legend Indicators -->
        </div>
    </div>
    <!-- End Row -->


    <!-- Bar Chart -->
    <div class="chartjs-custom">
      <?php
     $janBookingData = $total_sell[1];
      $febBookingData = $total_sell[2];
      $marBookingData = $total_sell[3];
      $aprBookingData = $total_sell[4];
      $mayBookingData = $total_sell[5];
      $junBookingData = $total_sell[6];
      $julBookingData = $total_sell[7];
      $augBookingData = $total_sell[8];
      $sepBookingData = $total_sell[9];
      $octBookingData = $total_sell[10];
      $novBookingData = $total_sell[11];
      $decBookingData = $total_sell[12];

      ?>


      <canvas id="updatingData" style="height: 20rem;"
      data-hs-chartjs-options='<?= json_encode([
          "type" => "bar",
          "data" => [
              "labels" => ["Jan", "Feb", "Mar", "April", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
              "datasets" => [[
                "data" => [$janBookingData, $febBookingData, $marBookingData, $aprBookingData, $mayBookingData, $junBookingData, $julBookingData, $augBookingData, $sepBookingData, $octBookingData, $novBookingData, $decBookingData],
                  "backgroundColor" => "#511281",
                  "hoverBackgroundColor" => "#511281",
                  "borderColor" => "#511281"
              ]]
          ],
          "options" => [
              "scales" => [
                  "yAxes" => [[
                      "gridLines" => [
                          "color" => "#e7eaf3",
                          "drawBorder" => false,
                          "zeroLineColor" => "#e7eaf3"
                      ],
                      "ticks" => [
                          "beginAtZero" => true,
                          "stepSize" => array_sum($total_sell) / 5,
                          "fontSize" => 12,
                          "fontColor" => "#97a4af",
                          "fontFamily" => "Open Sans, sans-serif",
                          "padding" => 10,
                          "postfix" => " " . \App\CentralLogics\Helpers::currency_symbol()
                      ]
                  ]],
                  "xAxes" => [[
                      "gridLines" => [
                          "display" => false,
                          "drawBorder" => false
                      ],
                      "ticks" => [
                          "fontSize" => 12,
                          "fontColor" => "#97a4af",
                          "fontFamily" => "Open Sans, sans-serif",
                          "padding" => 5
                      ],
                      "categoryPercentage" => 0.5,
                      "maxBarThickness" => "10"
                  ]]
              ],
              "cornerRadius" => 2,
              "tooltips" => [
                  "prefix" => " ",
                  "enabled" => false,
                  "hasIndicator" => true,
                  "mode" => "index",
                  "intersect" => false
              ],
              "hover" => [
                  "mode" => "nearest",
                  "intersect" => false,
                  "enabled" => false,
              ]
          ]
      ]); ?>'>
  </canvas>
  
    </div>
    <!-- End Bar Chart -->
</div>

<script>
  
   
</script>