<!-- Jquery -->
<script src="{{ asset('e-presensi') }}/assets/js/lib/jquery-3.4.1.min.js"></script>
<!-- Bootstrap-->
<script src="{{ asset('e-presensi') }}/assets/js/lib/popper.min.js"></script>
<script src="{{ asset('e-presensi') }}/assets/js/lib/bootstrap.min.js"></script>
<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<!-- Owl Carousel -->
<script src="{{ asset('e-presensi') }}/assets/js/plugins/owl-carousel/owl.carousel.min.js"></script>
<!-- jQuery Circle Progress -->
<script src="{{ asset('e-presensi') }}/assets/js/plugins/jquery-circle-progress/circle-progress.min.js"></script>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<!-- sweetalert2 -->
<script src="{{ asset('/AdminLTE/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/toastr/toastr.min.js') }}"></script>
@stack('script_vendor')

<!-- Base Js File -->
<script src="{{ asset('e-presensi') }}/assets/js/base.js"></script>

@stack('scripts')
<script>
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartdiv", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.legend = new am4charts.Legend();

        chart.data = [{
                country: "Hadir",
                litres: 501.9
            },
            {
                country: "Sakit",
                litres: 301.9
            },
            {
                country: "Izin",
                litres: 201.1
            },
            {
                country: "Terlambat",
                litres: 165.8
            },
        ];

        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "litres";
        series.dataFields.category = "country";
        series.alignLabels = false;
        series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
        series.labels.template.radius = am4core.percent(-40);
        series.labels.template.fill = am4core.color("white");
        series.colors.list = [
            am4core.color("#1171ba"),
            am4core.color("#fca903"),
            am4core.color("#37db63"),
            am4core.color("#ba113b"),
        ];
    }); // end am4core.ready()
</script>
