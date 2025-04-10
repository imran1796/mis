$().ready(function () {
    $sidebar = $('.sidebar');
    $sidebar_img_container = $sidebar.find('.sidebar-background');

    $full_page = $('.full-page');

    $sidebar_responsive = $('body > .navbar-collapse');

    window_width = $(window).width();

    // fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

    // if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
    //     if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
    //         $('.fixed-plugin .dropdown').addClass('show');
    //     }

    // }

    $('.fixed-plugin a').click(function (event) {
        // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
        if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else if (window.event) {
                window.event.cancelBubble = true;
            }
        }
    });

    $('.fixed-plugin .background-color span').click(function () {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('color');

        if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
        }

        if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
        }

        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
        }
    });

    $('.fixed-plugin .img-holder').click(function () {
        $full_page_background = $('.full-page-background');

        $(this).parent('li').siblings().removeClass('active');
        $(this).parent('li').addClass('active');


        var new_image = $(this).find("img").attr('src');

        if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function () {
                $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                $sidebar_img_container.fadeIn('fast');
            });
        }

        if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function () {
                $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
                $full_page_background.fadeIn('fast');
            });
        }

        if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
        }

        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
        }
    });

    $('.switch input').on("switchChange.bootstrapSwitch", function () {

        $full_page_background = $('.full-page-background');

        $input = $(this);

        if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
                $sidebar_img_container.fadeIn('fast');
                $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
                $full_page_background.fadeIn('fast');
                $full_page.attr('data-image', '#');
            }

            background_image = true;
        } else {
            if ($sidebar_img_container.length != 0) {
                $sidebar.removeAttr('data-image');
                $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
                $full_page.removeAttr('data-image', '#');
                $full_page_background.fadeOut('fast');
            }

            background_image = false;
        }
    });
});

type = ['primary', 'info', 'success', 'warning', 'danger'];

demo = {
    initPickColor: function () {
        $('.pick-class-label').click(function () {
            var new_class = $(this).attr('new-class');
            var old_class = $('#display-buttons').attr('data-class');
            var display_div = $('#display-buttons');
            if (display_div.length) {
                var display_buttons = display_div.find('.btn');
                display_buttons.removeClass(old_class);
                display_buttons.addClass(new_class);
                display_div.attr('data-class', new_class);
            }
        });
    },

    checkFullPageBackgroundImage: function () {
        $page = $('.full-page');
        image_src = $page.data('image');

        if (image_src !== undefined) {
            image_container = '<div class="full-page-background" style="background-image: url(' + image_src + ') "/>'
            $page.append(image_container);
        }
    },

    initDocumentationCharts: function () {
        /* ----------==========     Daily Sales Chart initialization For Documentation    ==========---------- */

        dataDailySalesChart2 = {
            labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
            series: [
                [12, 17, 7, 17, 23, 18, 38]
            ]
        };

        optionsDailySalesChart2 = {
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 0
            }),
            low: 0,
            high: 100, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            },
        }

        var dailySalesChart2 = new Chartist.Line('#dailySalesChart2', dataDailySalesChart2, optionsDailySalesChart2);

        // lbd.startAnimationForLineChart(dailySalesChart);
    },

    initDashboardPageCharts: function (chartData) {
        var dataPreferences = {
            series: [
                [25, 30, 20, 25]
            ]
        };

        var optionsPreferences = {
            donut: true,
            donutWidth: 40,
            startAngle: 0,
            total: 100,
            showLabel: true,
            axisX: {
                showGrid: false
            }
        };

        Chartist.Pie('#chartPreferences', dataPreferences, optionsPreferences);

        Chartist.Pie('#chartPreferences', {
            labels: ['50%', '35%', '15%'],
            series: [50, 35, 15]
        });

        var dataSales = {
            labels: ['9:00AM', '12:00AM', '3:00PM', '6:00PM', '9:00PM', '12:00PM', '3:00AM', '6:00AM','8PM'],
            series: [
                [287, 385, 490, 492, 554, 586, 698, 695,0],
                [67, 152, 143, 240, 287, 335, 435, 437,50,],
            ]
        };

        var optionsSales2 = {
            lineSmooth: false,
            low: 0,
            high: 800,
            chartPadding: 0,
            showArea: false,
            height: "245px",
            axisX: {
                showGrid: true,
            },
            axisY: {
                showGrid: true,
            },
            lineSmooth: Chartist.Interpolation.simple({
                divisor: 6
            }),
            showLine: true,
            showPoint: true,
            fullWidth: true
        };
        var optionsSales = {
            lineSmooth: false,
            low: 0,
            high: 800,
            showArea: true,
            height: "245px",
            axisX: {
                showGrid: false,
            },
            lineSmooth: Chartist.Interpolation.simple({
                divisor: 3
            }),
            showLine: false,
            showPoint: false,
            fullWidth: false
        };

        var responsiveSales = [
            ['screen and (max-width: 640px)', {
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0];
                    }
                }
            }]
        ];

        var chartHours = Chartist.Line('#chartHours', dataSales, optionsSales, responsiveSales);
        var chartHours2 = Chartist.Line('#chartHours2', dataSales, optionsSales2, responsiveSales);

        // lbd.startAnimationForLineChart(chartHours);

        var data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            series: [
                [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895],
                [412, 243, 280, 580, 453, 353, 300, 364, 368, 410, 636, 695]
            ]
        };

        var options = {
            seriesBarDistance: 10,
            axisX: {
                showGrid: false
            },
            height: "245px"
        };

        var responsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0];
                    }
                }
            }]
        ];

        var chartActivity = Chartist.Bar('#chartActivity', data, options, responsiveOptions);

        // lbd.startAnimationForBarChart(chartActivity);

        // /* ----------==========     Daily Sales Chart initialization    ==========---------- */
        //
        dataDailySalesChart = {
            labels: chartData.dateWiseDO.date,
            series: [
                chartData.dateWiseDO.count,
                chartData.dateWiseDO.dhaka_count

            ]
        };

        optionsDailySalesChart = {
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 2
            }),
            low: 0,
            high: 110, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: { top: 0, right: 0, bottom: 0, left: 0 },
              showLabel: false,
            axisY: {
                showGrid: true
            },
            plugins: [
                /*Chartist.plugins.ctPointLabels({
                    textAnchor: 'right',

                    labelInterpolationFnc: function (value) {

                        return value || ''; // Optional, if you want custom Y-axis labels
                    }
                })*/
            ]

        }
        var seriesLabelColors = ['#000', '#FF0000']; // First series red, second blue
        var dailySalesChart = Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);

        dailySalesChart.on('draw', function (context) {

            if (context.type === 'point' && context.value.y !== 0) {
               // context.element.remove(); // This removes the default label
                // Add labels to points with specific colors
                var labelColor = seriesLabelColors[context.seriesIndex]; // Determine the color based on the series
                var valueLabel = new Chartist.Svg('text');
                if(context.seriesIndex == 1){
                    valueLabel.attr({
                        x: context.x-12,
                        y: context.y -7 , // Adjust position above the point
                        'text-anchor': 'left',
                        style: `fill: ${labelColor}; font-size: 14px; `
                    });
                }else {
                    valueLabel.attr({
                        x: context.x + 2,
                        y: context.y - 10, // Adjust position above the point
                        'text-anchor': 'right',
                        style: `fill: ${labelColor}; font-size: 14px; `
                    });
                }
                valueLabel.text(context.value.y); // Display the point's value
                context.group.append(valueLabel); // Add the label to the chart
            }else if (context.type === 'point' && context.value.y === 0) {
                context.element.remove(); // This removes the default label

            }

        });

        // lbd.startAnimationForLineChart(dailySalesChart);

        //
        //
        // /* ----------==========     Completed Tasks Chart initialization    ==========---------- */
        //
        dataCompletedTasksChart = {
            labels: chartData.dateWiseDO.date,
            series: [
                chartData.dateWiseDO.count
            ]
        };

        optionsCompletedTasksChart = {
            lineSmooth: Chartist.Interpolation.cardinal({
                tension: 1
            }),
            low: 0,
            high: 100, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: { top: 0, right: 0, bottom: 0, left: 0 }
        }

        var completedTasksChart = new Chartist.Line('#completedTasksChart', dataCompletedTasksChart, optionsCompletedTasksChart);

        // start animation for the Completed Tasks Chart - Line Chart
        // lbd.startAnimationForLineChart(completedTasksChart);
        //
        //
        // /* ----------==========     Emails Subscription Chart initialization    ==========---------- */
        //
        var dataEmailsSubscriptionChart = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            series: [
                [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895]

            ]
        };
        var optionsEmailsSubscriptionChart = {
            axisX: {
                showGrid: false
            },
            low: 0,
            high: 1000,
            chartPadding: { top: 0, right: 5, bottom: 0, left: 0 }
        };
        var responsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0];
                    }
                }
            }]
        ];
        var emailsSubscriptionChart = Chartist.Bar('#emailsSubscriptionChart', dataEmailsSubscriptionChart, optionsEmailsSubscriptionChart, responsiveOptions);
        //
        // //start animation for the Emails Subscription Chart
        // lbd.startAnimationForBarChart(emailsSubscriptionChart);

    },

    initDashboardImportExportBar: function (data) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        let max = 0;
        function processData(data) {
            const result = Array(12).fill(0);
            data.forEach(item => {
                if (item.month >= 1 && item.month <= 12) {
                    result[item.month - 1] = Number(item.total_teuss);
                }
                if(Number(item.total_teuss) > max) {
                    max = Number(item.total_teuss);
                }
            });

            return result;
        }

        const importData = processData(data.import);
        const emptyData = processData(data.empty);
        const ladenData = processData(data.laden);

        function calculateRatios(importData, emptyData, ladenData) {
            return importData.map((value, index) => {
                const emptyLadenSum = ((emptyData[index] + ladenData[index])/value)*100;
                return emptyLadenSum === 0 ? '0:0' : `${100}:${Math.round(emptyLadenSum)}`;
            });
        }

        const ratioLabels = calculateRatios(importData, emptyData, ladenData);
        months.forEach((element, index) => {
            if (ratioLabels[index] === "100:NaN") {
                return;
            }
            months[index] = element +'<br>(' + ratioLabels[index]+')';
        });

        var data = {
            labels: months,
            series: [
                importData, emptyData, ladenData
            ]
        };

        var options = {
            seriesBarDistance: 20,
            axisX: {
                showGrid: false,
            },
            axisY: {
                high: max+1000,
            },
            height: "245px",
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class',
                    labelInterpolationFnc: function (value) {
                        // Customize label text
                        return value;
                    },
                    // rotation: 90,
                    labelOffset: {
                         x: 5,  // Adjust horizontal offset
                         y: -35   // Adjust vertical offset
                    },
                    textAnchor: 'middle',
                    // showZeroLabels: true,
                    // includeIndexClass: true,
                    // thresholdOptions: {
                    //     percentage: 25,
                    //     belowLabelClass: 'ct-label-below',
                    //     aboveLabelClass: 'ct-label-above'
                    // }
                }),
            ]
        };

        var responsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5,
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0];
                    }
                }
            }]
        ];

        Chartist.Bar('#importExportData', data, options, responsiveOptions);
    },

    initExportDataBar: function (labels, series, max_val) {
        var data = {
            labels: labels,
            series: series
        };

        var options = {
            seriesBarDistance: 20, // Distance between bars
            axisX: {
                showGrid: false, // Hide grid lines for X-axis
            },
            axisY: {
                high:  max_val, // Adjust Y-axis high value dynamically
                offset: 60, // Optional: This will provide more spacing at the bottom for labels
            },
            height: "245px", // Set height of the chart
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class', // Custom label class
                    labelInterpolationFnc: function (value) {
                        return value; // Customize label text if needed
                    },
                    labelOffset: {
                        x: 5,  // Adjust horizontal offset for the label
                        y: -35  // Adjust vertical offset for the label
                    },
                    textAnchor: 'middle', // Align text at the middle of the bars
                }),
            ],
        };
        
        var responsiveOptions = [
            ['screen and (max-width: 640px)', {
                seriesBarDistance: 5, // Closer bars on small screens
                axisX: {
                    labelInterpolationFnc: function (value) {
                        return value[0]; // Shorten labels for small screens (optional)
                    }
                }
            }]
        ];
        
        // Use the updated options and data
        Chartist.Bar('#chart1', data, options, responsiveOptions);
    },

    initExportDataChart: function (chartData) {
        let total = chartData.reduce((sum, value) => sum + value, 0); 
        if (total === 0) { 
            chartData = chartData.map(() => 0); 
        } else {
            chartData = chartData.map(value => parseFloat(((value / total) * 100).toFixed(2))); 
        }

        var dataPreferences = {
            series: chartData
        };
    
        var optionsPreferences = {
            donut: true,
            donutWidth: 40,
            startAngle: 0,
            total: chartData.reduce((sum, value) => sum + value, 0),
            showLabel: true
        };
    
        // Initialize the Pie chart correctly without axis settings
        Chartist.Pie('#chartPie', dataPreferences, optionsPreferences);
    },
    

    initGoogleMaps: function () {
        var myLatlng = new google.maps.LatLng(40.748817, -73.985428);
        var mapOptions = {
            zoom: 13,
            center: myLatlng,
            scrollwheel: false, //we disable de scroll over the map, it is a really annoing when you scroll through page
            styles: [{
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#e9e9e9"
                }, {
                    "lightness": 17
                }]
            }, {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 17
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 29
                }, {
                    "weight": 0.2
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 18
                }]
            }, {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            }, {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 21
                }]
            }, {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dedede"
                }, {
                    "lightness": 21
                }]
            }, {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "on"
                }, {
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            }, {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "saturation": 36
                }, {
                    "color": "#333333"
                }, {
                    "lightness": 40
                }]
            }, {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f2f2f2"
                }, {
                    "lightness": 19
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 17
                }, {
                    "weight": 1.2
                }]
            }]
        };

        var map = new google.maps.Map(document.getElementById("map"), mapOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            title: "Hello World!"
        });

        // To add the marker to the map, call setMap();
        marker.setMap(map);
    },

    showNotification: function (from, align) {
        color = Math.floor((Math.random() * 4) + 1);

        $.notify({
            icon: "nc-icon nc-app",
            message: "Welcome to <b>Light Bootstrap Dashboard</b> - a beautiful freebie for every web developer."

        }, {
            type: type[color],
            timer: 8000,
            placement: {
                from: from,
                align: align
            }
        });
    },

    customShowNotification: function (type, msg) {
        color = Math.floor((Math.random() * 4) + 1);

        $.notify({
            icon: "nc-icon nc-app",
            message: msg
        }, {
            type: type,
            timer: 8000,
            placement: {
                from: 'top',
                align: 'right',
            }
        });
    }

}
