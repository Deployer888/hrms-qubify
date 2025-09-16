Highcharts.chart('container', {

    chart: {
      type: 'gauge',
      plotBackgroundColor: null,
      plotBackgroundImage: null,
      plotBorderWidth: 0,
      plotShadow: false,
      height: '80%'
    },
    
    title: {
      text: 'Speedometer'
    },
    
    pane: {
      startAngle: -90,
      endAngle: 89.9,
      background: null,
      center: ['50%', '75%'],
      size: '110%'
    },
    
    // the value axis
    yAxis: {
      min: 0,
      max: 100,
      tickPixelInterval: 72,
      tickPosition: 'inside',
      tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
      tickLength: 50,
      tickWidth: 0,
      minorTickInterval: null,
      labels: {
          distance: 20,
          style: {
              fontSize: '15px'
          }
      },
      lineWidth: 0,
      plotBands: [{
          from: 0,
          to: 40,
          color: 'red', // green
          thickness: 50,
          borderRadius: '0%'
      }, {
          from: 40,
          to: 75,
          color: 'orange', // red
          thickness: 50,
          borderRadius: '0%'
      }, {
          from: 75,
          to: 100,
          color: 'green', // yellow
          thickness: 50
      }]
    },


});