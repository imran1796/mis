let globalSalesData = [];
$(() => {
    const routeDashboardData = window.dashboardRoutes.getData;
    const csrfToken = window.csrfToken;
    const today = new Date();
    const defaultMonth = today.getMonth() + 1;
    const defaultYear = today.getFullYear();
    const defaultGearTypes = ['g', 'gl'];
    const defaultCtnSizes = ['dc20','r20','dc40','dc45','r40','mty20','mty40' ];
    const defaultOperators = [];

    // === INIT CHECKBOXES ===
    autoCheckInputs('months[]', [defaultMonth]);
    autoCheckInputs('years[]', [defaultYear]);
    autoCheckInputs('gear_types[]', defaultGearTypes);
    autoCheckInputs('operators[]', defaultOperators);
    autoCheckInputs('ctn_sizes[]', defaultCtnSizes);

    // === INITIAL DASHBOARD LOAD ===
    fetchDashboard({
        months: [defaultMonth],
        years: [defaultYear],
        gear_types: defaultGearTypes,
        operators: defaultOperators,
        ctn_sizes: defaultCtnSizes 
    });

    // === Filter change handler ===
    $(document).on('change', '.filter-checkbox', () => {
        const payload = {
            months: getCheckedValues('months[]'),
            years: getCheckedValues('years[]'),
            operators: getCheckedValues('operators[]'),
            gear_types: getCheckedValues('gear_types[]'),
            ctn_sizes: getCheckedValues('ctn_sizes[]')
        };
        fetchDashboard(payload);
    });

    // Filter Commodity
    $('#commodityVsOtherChart').on('change', function(){

    });

    // === FUNCTIONS ===

    function autoCheckInputs(name, defaults = []) {
        $(`input[name="${name}"]`).each(function () {
            const value = $(this).val().toLowerCase();
            if (defaults.map(d => String(d).toLowerCase()).includes(value)) {
                $(this).prop('checked', true);
                // $(this).closest('label').addClass('active btn-primary').removeClass('btn-outline-primary');
                $(this).closest('label').addClass('active');
            }
        });
    }

    function getCheckedValues(name) {
        return $(`input[name="${name}"]:checked`)
            .map((_, el) => el.value)
            .get();
    }

    function fetchDashboard(filters) {
        $.ajax({
            url: routeDashboardData,
            type: 'POST',
            data: { ...filters, _token: csrfToken },
            success: handleDashboardResponse,
            error: () => showNotify('Something went wrong', 'danger')
        });
    }

    function handleDashboardResponse(res) {
        const turnAround = res?.turnAroundData ?? {};
        const ops = res?.operatorWiseHandling ?? {};
        const salesData = globalSalesData = res?.salesData ??{}

        $('#totalVesselCall').text(`${turnAround.totalVesselCall ?? 0} calls`);
        $('#avgAnchorageTime').text(`${turnAround.avgAnchorageTime ?? 0} HRS`);
        $('#avgBerthTime').text(`${turnAround.avgBerthTime ?? 0} HRS`);
        $('#avgTurnAroundTime').text(`${turnAround.avgTurnAroundTime ?? 0} HRS`);
        $('#avgBoxHandling').text(`${turnAround.avgBox??0} Box`);
        $('#avgImportTeu').text(`${turnAround.avgImpTeu??0} TEUs`);
        $('#avgExportTeu').text(`${turnAround.avgExpTeu??0} TEUs`);
        $('#ttlVesselByCrane').html(`Geared: ${turnAround.geared ?? 0}<br>Gearless: ${turnAround.gearless ?? 0}`);

        initBarChart('#exportData', ops.export);
        initBarChart('#importData', ops.import);
        initCommodityTueBarChart('#commodityTeuBarChart', salesData);
        initSnkOtherPODTeuChart('#snkOtherPODTeuChart', salesData);
        initRegionTeuChart('#regionTeuChart', salesData);
        initCommodityVsOthersChart('#gmtsPercentageChart', salesData, 'GMTS');
        // initPodMLOCommodityTueBarChart('#podMLOCommodityTeuBarChart', salesData,'SKN');
        // initCommodityPodTeuChart('#podMLOCommodityTeuBarChart', salesData, 'MSC');
        // initGmtsPercentageChart('#gmtsPercentageChart',salesData);

        populateDropdown(salesData);
    }

    function populateDropdown(data) {
        const dropdown = document.getElementById('commodityVsOtherChart');
        // const dropdownMLO = document.getElementById('mloComPodChart');
        const commodities = [...new Set(data.map(item => item.commodity?.toUpperCase()).filter(Boolean))].sort();
        const mlos = [...new Set(data.map(item => item.mlo?.toUpperCase()).filter(Boolean))].sort();
    
        dropdown.innerHTML = commodities.map(c =>
            `<option value="${c}" ${c === 'GMTS' ? 'selected' : ''}>${c}</option>`
        ).join('');
        // dropdownMLO.innerHTML = mlos.map(c =>
        //     `<option value="${c}" ${c === 'SKN' ? 'selected' : ''}>${c}</option>`
        // ).join('');
    }

    function padByDigitLength(number) {
        const length = number.toString().length;
        const halfStep = Math.pow(10, length - 1);
        return number + halfStep;
    }    

    
    document.getElementById('commodityVsOtherChart').addEventListener('change', function () {
        const selected = this.value;
        initCommodityVsOthersChart('#gmtsPercentageChart', globalSalesData, selected);
    });

    // document.getElementById('mloComPodChart').addEventListener('change', function () {
    //     const selected = this.value;
    //     initPodMLOCommodityTueBarChart('#podMLOCommodityTeuBarChart', globalSalesData, selected);
    // });

    function initBarChart(selector, data) {
        if (!data || Object.keys(data).length === 0) {
            data = {
                Singapore: {
                    laden: 0,
                    empty: 0
                },
                Colombo: {
                    laden: 0,
                    empty: 0
                },
                Kolkata: {
                    laden: 0,
                    empty: 0
                }
            };
        }
        
        const routes = Object.keys(data);
        const emptyData = routes.map(r => Number(data[r].empty) || 0.1);
        const ladenData = routes.map(r => Number(data[r].laden) || 0.1);
        const maxValue = Math.max(
            ...routes.map(r => Math.max(Number(data[r].empty) || 0, Number(data[r].laden) || 0))
        );

        new Chartist.Bar(selector, {
            labels: routes,
            series: [emptyData, ladenData]
        }, {
            seriesBarDistance: 20,
            axisX: { showGrid: false },
            axisY: { high: padByDigitLength(maxValue) },
            height: "245px",
            chartPadding: { left: 22 },
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class',
                    labelInterpolationFnc: value => (value == 0.1 ? '0' : value),
                    labelOffset: { x: 5 },
                    textAnchor: 'middle'
                }),
                // Chartist.plugins.tooltip()
            ]
        });
    }

    function initCommodityTueBarChart(selector,data){
        const topCommodities = getTopCommodities(data);
        
        const labels = topCommodities.map(c => c.commodity);
        const series = topCommodities.map(c => c.total_teu);

        new Chartist.Bar(selector, {
            labels: labels,
            series: [series]
        }, {
            seriesBarDistance: 20,
            axisX: { showGrid: false },
            axisY: {
                high: padByDigitLength(Math.max(...series))
            },
            height: "300px",
            chartPadding: { left: 22, bottom: 60 },
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class',
                    labelInterpolationFnc: value => (value == 0.1 ? '0' : value),
                    labelOffset: { x: 5 },
                    textAnchor: 'middle'
                }),
            ]
        });
        
    }

    
    function getTopCommodities(data, topN = 20) {
        if (!Array.isArray(data)) return [];
    
        const grouped = {};
    
        // Group by commodity and sum total_teu
        data.forEach(item => {
            const key = item.commodity;
            const teu = parseFloat(item.total_teu) || 0;
    
            if (!grouped[key]) {
                grouped[key] = 0;
            }
            grouped[key] += teu;
        });
    
        // Convert to array and sort descending
        const sorted = Object.entries(grouped)
            .map(([commodity, total_teu]) => ({ commodity, total_teu }))
            .sort((a, b) => b.total_teu - a.total_teu);
    
        // Take top N
        return sorted.slice(0, topN);
    }

    // snkOtherPODTeuChart
    function initSnkOtherPODTeuChart(selector,data){
        const { labels, series } = getSknVsOthersTeuByPod(data);
        
        new Chartist.Bar(selector, {
            labels: labels,
            series: series // [SKN, OTHERS]
        }, {
            seriesBarDistance: 20,
            axisX: {
                showGrid: false,
                offset: 60 // enough space for long pod names
            },
            axisY: {
                high: padByDigitLength(Math.max(...series.flat())),
                onlyInteger: true
            },
            height: "350px",
            chartPadding: {
                left: 22,
                bottom: 60
            },
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class',
                    labelInterpolationFnc: value => (value == 0.1 ? '0' : value),
                    labelOffset: { x: 5 },
                    textAnchor: 'middle'
                }),
            ]
        });
        
    }

    function initRegionTeuChart(selector,data){
        // const topCommodities = getTopCommodities(data);
        const regionWiseTue = getRegionWiseTue(data);
        // console.log(regionWiseTue);
        
        const labels = regionWiseTue.map(c => c.trade);
        const series = regionWiseTue.map(c => c.total_teu);
        // console.log(labels,series);

        new Chartist.Bar(selector, {
            labels: labels,
            series: [series]
        }, {
            seriesBarDistance: 20,
            axisX: { showGrid: false },
            axisY: {
                high: padByDigitLength(Math.max(...series))
            },
            height: "300px",
            chartPadding: { left: 22, bottom: 60 },
            plugins: [
                Chartist.plugins.ctBarLabels({
                    labelClass: 'custom-label-class',
                    labelInterpolationFnc: value => (value == 0.1 ? '0' : value),
                    labelOffset: { x: 5 },
                    textAnchor: 'middle'
                }),
            ]
        });
        
    }

    function initCommodityVsOthersChart(selector, data, selectedCommodity = 'GMTS') {
        const target = selectedCommodity.toUpperCase();
        $('#currentCommodity').text(target);
    
        const targetData = data.filter(item => item.commodity?.toUpperCase() === target);
        const otherData = data.filter(item => item.commodity?.toUpperCase() !== target);
    
        const totalTargetTeu = targetData.reduce((sum, item) => sum + (parseFloat(item.total_teu) || 0), 0);
        const totalOtherTeu = otherData.reduce((sum, item) => sum + (parseFloat(item.total_teu) || 0), 0);
        const totalAll = totalTargetTeu + totalOtherTeu;
    
        const targetPercent = totalTargetTeu > 0 ? ((totalTargetTeu / totalAll) * 100).toFixed(2) : 0;
        const othersPercent = (100 - targetPercent).toFixed(2);
    
        // Render Donut Chart
        new Chartist.Pie(selector, {
            labels: [`${othersPercent}%`, `${targetPercent}%`],
            series: [parseFloat(othersPercent), parseFloat(targetPercent)]
        }, {
            donut: true,
            donutWidth: 40,
            showLabel: true,
            height: '250px',
        });
    
        // Grouping & displaying info
        const mloCounts = groupAndPercent(targetData, 'mlo', totalTargetTeu);
        const podCounts = groupAndPercent(targetData, 'pod', totalTargetTeu);
        const regionCounts = groupAndPercent(targetData, 'trade', totalTargetTeu);
    
        const podInfoContainer = document.querySelector('#gmtsPrecentageChartPODInfoContainer');
        podInfoContainer.innerHTML = `<h6>${formatList(podCounts, 'pod')}</h6>`;
    
        const infoContainer = document.querySelector('#gmtsPrecentageChartInfoContainer');
        infoContainer.classList.add('text-left');
        infoContainer.innerHTML = `
            <h6><strong>MLO:</strong> ${formatList(mloCounts, 'mlo')}</h6>
            <h6><strong>Region:</strong> ${formatList(regionCounts, 'region')}</h6>
        `;
    }
    
    function groupAndPercent(data, key, totalTeu) {
        const map = {};
        // if(key=='pod'){console.log(data)}
    
        data.forEach(item => {
            const k = item[key]?.toUpperCase() || '---';
            const teu = parseFloat(item.total_teu) || 0;
            map[k] = (map[k] || 0) + teu;
        });
    
        return Object.entries(map)
            .map(([k, teu]) => ({ label: k, percent: ((teu / totalTeu) * 100).toFixed(1) }))
            .sort((a, b) => b.percent - a.percent)
            .slice(0, key=='pod'?20:10);
    }
    
    function formatList(arr,key) {
        return arr.map(item => `${item.label} (${item.percent}%)`).join(key=='pod'?'<br>':', ');
    }
    
    function getRegionWiseTue(data){
        const grouped = {};
        // console.log(data);
        data.forEach(item=>{
            const key = item?.trade?.trim() ? item.trade.trim() : '---';
            const tue  = parseFloat(item.total_teu) ||0;
            if(!grouped[key]){
                grouped[key]=0;
            }
            grouped[key]+=tue;
        })

        // Convert to array and sort descending
        const sorted = Object.entries(grouped)
            .map(([trade, total_teu]) => ({ trade, total_teu }))
            .sort((a, b) => b.total_teu - a.total_teu);
    
        // Take top N
        return sorted;
    }

    function getSknVsOthersTeuByPod(data) {
        // console.log(data,'iiii');
        if (!Array.isArray(data)) return { labels: [], series: [[], []] };
    
        const SKN_MLO = 'SKN';
        const sknPods = new Set();
    
        const podTeuMap = {
            SKN: {},   // { pod1: teu, pod2: teu, ... }
            OTHERS: {} // same pods as SKN
        };
    
        // Collect SKN pod data
        data.forEach(item => {
            // console.log(item);
            const pod = item.pod?.toUpperCase();
            const mlo = item.mlo?.toUpperCase();
            const teu = parseFloat(item.total_teu) || 0;
    
            if (!pod) return;
    
            if (mlo === SKN_MLO) {
                sknPods.add(pod);
                let tempData = ((podTeuMap.SKN[pod] || 0) + teu) ? ((podTeuMap.SKN[pod] || 0) + teu) : 0.1;
                podTeuMap.SKN[pod] = tempData;
            }
        });
    
        // Collect OTHERS data for only SKN pods
        data.forEach(item => {
            const pod = item.pod?.toUpperCase();
            const mlo = item.mlo?.toUpperCase();
            const teu = parseFloat(item.total_teu) || 0;
    
            if (!pod || mlo === SKN_MLO || !sknPods.has(pod)) return;

            let tempData = ((podTeuMap.OTHERS[pod] || 0) + teu) >0 ? ((podTeuMap.OTHERS[pod] || 0) + teu) : 0.1;
            podTeuMap.OTHERS[pod] = tempData;
        });
    
        const labels = Array.from(sknPods);
        const sknSeries = labels.map(pod => podTeuMap.SKN[pod] || 0.1);
        const othersSeries = labels.map(pod => podTeuMap.OTHERS[pod] || 0.1);
    
        return { labels, series: [sknSeries, othersSeries] };
    }

    // function initPodMLOCommodityTueBarChart(selector, data, selectedMlo) {
    //     const processed = getTopCommoditiesWithTopPods(data, selectedMlo);
    //     const commodities = Object.keys(processed); // x-axis

    //     const uniquePods = Array.from(
    //         new Set(commodities.flatMap(c => processed[c].map(entry => entry.pod)))
    //     );
    
    //     // Prepare series: one series per POD, each with TEUs per commodity (0 if missing)
    //     const series = uniquePods.map(pod => {
    //         return commodities.map(commodity => {
    //             const match = processed[commodity].find(entry => entry.pod === pod);
    //             return match
    //                 ? { value: match.total_teu, meta: `${pod}` }
    //                 : { value: 0, meta: `${pod}` };
    //         });
    //     });
    //     //---

    //     // console.log(series.meta,'==');
    //     const portListHtml = uniquePods.map(pod => `<span class="badge bg-primary me-1">${pod}</span>`).join('');
    //     console.log(portListHtml,uniquePods,"=");
    //     $('.podMloCommodityTeuBarInfo').html(portListHtml);
        
    
    //     new Chartist.Bar(selector, {
    //         labels: commodities,
    //         series: series // each value: { value: number, meta: pod }
    //     }, {
    //         axisX: { showGrid: false },
    //         axisY: {
    //             offset: 50,
    //             onlyInteger: true,
    //             // labelInterpolationFnc: value => value,
    //             high: Math.max(...series.flat().map(d => d.value)) + 500
    //         },
    //         seriesBarDistance: 20,
    //         height: "300px",
    //         chartPadding: { left:15, bottom: 60 },
    //     }).on('draw', function (data) {
    //         if (data.type === 'bar') {
    //           const meta = data.meta || '';
    //           const value = data.value.y || '0';
    //         //   console.log(meta, value,"s");
    //           const label = `${meta}\n${value}`;
    //         //   console.log(data);
          
    //           data.group.elem('text', {
    //             x: data.x1,
    //             y: data.y1,
    //             style: 'font-size: 14px;',
    //                     'text-anchor': 'end',
    //                     'writing-mode': 'sideways-lr'
    //           }).text(label);
    //         }
        
    //     });
        
        
    // }
//     function initPodMLOCommodityTueBarChart(selector, data, selectedMlo) {
//     const processed = getTopCommoditiesWithTopPods(data, selectedMlo);
//     const commodities = Object.keys(processed);
//     const uniquePods = Array.from(
//         new Set(commodities.flatMap(c => processed[c].map(entry => entry.pod)))
//     );

//     const series = uniquePods.map(pod => {
//         return commodities.map(commodity => {
//             const match = processed[commodity].find(entry => entry.pod === pod);
//             return match
//                 ? { value: match.total_teu, meta: `${pod}` }
//                 : { value: 0, meta: `${pod}` };
//         });
//     });

//     //--
//     const chartElement = document.querySelector(selector);

//     // Calculate how many bars (commodities * pods)
//     const estimatedBarWidth = 40;
//     const groupMargin = 20;
//     const barGroups = commodities.length;
//     const barsPerGroup = uniquePods.length;
//     const estimatedWidth = (barGroups * barsPerGroup * estimatedBarWidth)
//                         + (barGroups * groupMargin);

//     // Ensure width doesn't shrink below 100%
//     const containerWidth = chartElement.parentElement.offsetWidth;
//     chartElement.style.width = estimatedWidth > containerWidth
//         ? `${estimatedWidth}px`
//         : "100%";

        

//     //--

//     // Render port info badges
//     const portListHtml = uniquePods.map(pod => `<span class="badge bg-primary me-1">${pod}</span>`).join('');
//     $('.podMloCommodityTeuBarInfo').html(portListHtml);

//     // Init Chart
//     new Chartist.Bar(selector, {
//         labels: commodities,
//         series: series
//     }, {
//         axisX: { showGrid: false },
//         axisY: {
//             offset: 50,
//             onlyInteger: true,
//             high: Math.max(...series.flat().map(d => d.value)) + 500
//         },
//         seriesBarDistance: 20,
//         height: "300px",
//         chartPadding: { left: 22, bottom: 60 },
//     }).on('draw', function (data) {
//         if (data.type === 'bar') {
//             const meta = data.meta || '';
//             const value = data.value.y || '0';
//             const label = `${meta}\n${value}`;
//             data.group.elem('text', {
//                 x: data.x1,
//                 y: data.y1,
//                 style: 'font-size: 11px;',
//                 'text-anchor': 'end',
//                 'writing-mode': 'sideways-lr'
//             }).text(label);
//         }
//     });
// }


//     function getTopCommoditiesWithTopPods(data, selectedMlo, topComCount = 5, topPodCount = 3) {
//         if (!Array.isArray(data)) return {};
    
//         const commodityPodMap = {};
//         const commodityTotals = {};
    
//         // Step 1: Group TEU by commodity and pod for selected MLO
//         data.forEach(item => {
//             if (item.mlo !== selectedMlo) return;
    
//             const com = item.commodity || 'UNKNOWN';
//             const pod = item.pod;
//             const teu = parseFloat(item.total_teu) || 0;
    
//             if (!commodityPodMap[com]) commodityPodMap[com] = {};
//             commodityPodMap[com][pod] = (commodityPodMap[com][pod] || 0) + teu;
    
//             // For total TEU per commodity
//             commodityTotals[com] = (commodityTotals[com] || 0) + teu;
//         });
    
//         // Step 2: Sort commodities by total TEU
//         const sortedCommodities = Object.entries(commodityTotals)
//             .sort((a, b) => b[1] - a[1]) // Descending by TEU
//             .map(([com]) => com);
    
//         const result = {};
//         let addedComCount = 0;
    
//         // Step 3: Include only commodities that have at least one valid pod
//         for (const com of sortedCommodities) {
//             if (addedComCount >= topComCount) break;
    
//             const podData = Object.entries(commodityPodMap[com] || {})
//                 .map(([pod, teu]) => ({ pod, total_teu: teu }))
//                 .filter(item => item.total_teu > 0) // Exclude zero TEU
//                 .sort((a, b) => b.total_teu - a.total_teu)
//                 .slice(0, topPodCount);
    
//             if (podData.length > 0) {
//                 result[com] = podData;
//                 addedComCount++;
//             }
//         }
    
//         return result;
//     }
    
    function showNotify(message, type = 'info') {
        $.notify({ icon: "add_alert", message }, {
            type,
            timer: 1000,
            placement: { from: 'top', align: 'right' },
        });
    }

});
