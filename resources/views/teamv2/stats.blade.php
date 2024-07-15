@extends('teamv2.layout.layout')

@section('content')

 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="nav-icon fas fa-tachometer-alt"></i> Tableau de bord</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
              <li class="breadcrumb-item active">Tableau de bord</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="container-fluid">
        
        <div class="beamble-row">
          <div>
            <div class="info-box bg-success bg-beamble">
              <span class="info-box-icon"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text"><a href="/users" class="text-light">
                <i class="fas fa-arrow-circle-right"></i> Utilisateurs
              </a></span>
                <span class="info-box-number">{{ App\Models\User::count() }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div> -->
                <span class="text-xs">
                  All users in database.
                  {{ App\Models\User::where('is_generic',1)->count() }}
                </span> 
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div>
            <div class="info-box bg-success bg-beamble">
              <span class="info-box-icon"><img class="beamble-icon" src="/team/imgs/app_icon-b.png"></span>

              <div class="info-box-content">
                <span class="info-box-text">Clients qui s'engagent à moins d'un paiement</span>
                <span class="info-box-number">{{ App\Models\UserStripeCustomer::where('customer_stripe_enabled',1)->count()  }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span> -->
              </div> 
              <!-- /.info-box-content -->
            </div>
          </div>
          <div>
            <div class="info-box bg-success bg-beamble">
              <span class="info-box-icon"><img class="beamble-icon" src="/team/imgs/app_icon-b.png"></span>

              <div class="info-box-content">
                <span class="info-box-text">Beamers habilités par Stripe pour recevoir</span>
                <span class="info-box-number">{{ App\Models\UserStripeAccount::where('account_stripe_enabled',1)->count() }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span>  -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <div class="beamble-row ">
          <div>
            <div class="info-box bg-success">
              <span class="info-box-icon"><i class="fas fa-video"></i></span>

              <div class="info-box-content">
                <span class="info-box-text"><strong>Freemium</strong> appels</span>
                <span class="info-box-number">{{ App\Models\Videocall::whereNotNull('meeting_id')->where('is_freemium',1)->count()  }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="text-sm">
   
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div>
            <div class="info-box bg-success">
              <span class="info-box-icon"><i class="fas fa-video"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Appels vidéo </span>
                <span class="info-box-number">{{ App\Models\Videocall::whereNotNull('meeting_id')->count()  }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="text-sm">
   
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div>
            <div class="info-box bg-success">
              <span class="info-box-icon"><i class="far fa-clock"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Temps d'appel moyen</span>
                <span class="info-box-number">{{ round( App\Models\Videocall::where('status','success_with_duration')->avg('duration') ) }} <small>secondes</small></span>
                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div>
            <div class="info-box bg-warning">
              <span class="info-box-icon"><i class="fas fa-comments"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Contacts</span>
                <span class="info-box-number">{{ App\Models\Webview\Contacts::count()  }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div>
            <div class="info-box bg-danger">
              <span class="info-box-icon"><i class="fas fa-wifi"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Beamer en ligne maintenant</span>
                <span class="info-box-number">{{ App\Models\UserTrack::whereNotNull('lat')->where('status','on')->count() }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div>
            <div class="info-box bg-danger">
              <span class="info-box-icon"><i class="fas fa-wifi"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Appels actifs maintenant</span>
                <span class="info-box-number">{{ App\Models\Videocall::where('status','accepted')->count() }}</span>

                <!-- <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  70% Increase in 30 Days
                </span> -->
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>


        <div class="row">
          <div class="col-sm">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Appels vidéo</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body" style="padding:0">
                <div class="chart">
                  <div id="lineChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>


      </div>
    </section>

@endsection

@section('footer_scripts')
<script src="{{ asset('team/adminlte/plugins/uplot/uPlot.iife.min.js') }}"></script>
<script>
  var ts;
  $(function () {
    /* uPlot
     * -------
     * Here we will create a few charts using uPlot
     */

    function getSize(elementId) {
      return {
        width: document.getElementById(elementId).offsetWidth,
        height: document.getElementById(elementId).offsetHeight - 50,
      }
    }
    function padTo2Digits(num) {
      return num.toString().padStart(2, '0');
    }
    function formatDate(date) {
      return [
        padTo2Digits(date.getMonth() + 1),
        date.getFullYear(),
      ].join('/');
    }

        <?php
          $stats = App\Models\Videocall::stats();
          $max = 0;
          $list1 = [];
          $list2 = [];
          $list3 = [];
          foreach($stats['calls'] as $tipo=>$valores){
            foreach($valores as $mes_semana => $total){
                if($total>$max){
                  $max = $total;
                }
              
              $ano_mes = explode(":", $mes_semana);
              $ano = $ano_mes[0];
              $week = $ano_mes[1];

              $semana_1 = date('W', strtotime($ano . '-01'));

              $multiplic = (int) $week - (int) $semana_1;
              // dd($multiplic);
              // $pont = str_repeat(".", $multiplic);
              $dia_ref = 1 + ( 7 * $multiplic);
              $dia_ref = str_pad($dia_ref, 2, "0", STR_PAD_LEFT);
              $mes = date($dia_ref.' F Y', strtotime($ano . '-'.$dia_ref));
              $list1[] =  $mes;
              if($tipo=='freemium'){
                $list3[] =  $total;
              } else {
                $list2[] =  $total;
              }
            }
          }
          $list1 = array_unique($list1);
          $list1 = array_values($list1);
          /**
        Array
(
    [freemium] => Array
        (
            [2023-02:07] => 0
            [2023-07:29] => 0
            [2023-08:31] => 0
            [2023-08:34] => 0
            [2023-09:36] => 1
        )

    [notfreemium] => Array
        (
            [2023-02:07] => 6
            [2023-07:29] => 1
            [2023-08:31] => 1
            [2023-08:34] => 1
            [2023-09:36] => 0
        )

)
           */
          ?>

    var prets = <?php echo json_encode($list1); ?>;
    ts = [];
    prets.forEach(y => {
        ts.push(Date.parse(y)/1000);
    });
    // Date.parse("2019-01-01");

    let data = [
      ts,
      <?php echo json_encode($list2); ?>,
      <?php echo json_encode($list3); ?>
    ];

    //--------------
    //- AREA CHART -
    //--------------

    const optsLineChart = {
      ... getSize('lineChart'),
      tzDate: ts => uPlot.tzDate(new Date(ts * 1e3), 'Etc/UTC'),
      scales: {
        // x: {
        //   time: true,
        // },
        y: {
          range: [0, {{ $max }}],
        },
      },
      series: [
        {
          label: "Date",
        },
        {
          fill: 'transparent',
          width: 5,
          label: "Appels",
          stroke: 'rgba(60,141,188,1)',
        },
        {
          stroke: '#c1c7d1',
          label: "Freemium appels",
          width: 5,
          fill: 'transparent',
        },
      ],
      axes: [
          {
            space: (self, axisIdx, scaleMin, scaleMax, plotDim) => {
              let rangeSecs = scaleMax - scaleMin;
              let rangeDays = rangeSecs / 86400;
              let pxPerDay = plotDim / rangeDays;
              // ensure min split space is 28 days worth of pixels
              return pxPerDay * 28;
            },
          },
      ]
    };

    let lineChart = new uPlot(optsLineChart, data, document.getElementById('lineChart'));

    window.addEventListener("resize", e => {
      lineChart.setSize(getSize('lineChart'));
    });
  })
</script>

@endsection