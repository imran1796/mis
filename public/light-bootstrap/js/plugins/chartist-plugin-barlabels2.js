!(function (a, b) {
    "function" == typeof define && define.amd
        ? define([], function () {
              return (a.returnExportsGlobal = b());
          })
        : "object" == typeof exports
        ? (module.exports = b())
        : (a["Chartist.plugins.ctBarLabels"] = b());
})(this, function () {
    return (
        (function (a, b) {
            "function" == typeof define && define.amd
                ? define([], function () {
                      return (a.returnExportsGlobal = b());
                  })
                : "object" == typeof exports
                ? (module.exports = b())
                : (a["Chartist.plugins.ctBarLabels"] = b());
        })(this, function () {
            function a(a) {
                if (a.options.horizontalBars && a.options.axisX && a.options.axisX.high) return a.options.axisX.high;
                if (!a.options.horizontalBars && a.options.axisY && a.options.axisY.high) return a.options.axisY.high;
                if (a.options.high) return a.options.high;
                if (a.data && a.data.series && a.data.series.length > 0) {
                    var b = a.data.series;
                    return (
                        b[0].constructor === Array &&
                            (b = b.reduce(function (a, b) {
                                return a.concat(b);
                            })),
                        Math.max.apply(null, b)
                    );
                }
            }
            function b(a, b, c, d) {
                return a && b && c ? ((d / c) * 100 > a ? b.aboveLabelClass : b.belowLabelClass) : "";
            }
            function c(a, b, c, d) {
                if (!a) return {};
                var e = a({ high: b, value: c, threshold: d }),
                    f = {};
                return e.labelOffset && ((f.labelOffset = e.labelOffset), e.labelOffset.x && (f.labelOffset.x = e.labelOffset.x), e.labelOffset.y && (f.labelOffset.y = e.labelOffset.y)), e.textAnchor && (f.textAnchor = e.textAnchor), f;
            }
            return (
                (function (d, e, f) {
                    "use strict";
                    var g = {
                            labelClass: "ct-label",
                            labelInterpolationFnc: f.noop,
                            labelPositionFnc: void 0,
                            showZeroLabels: !1,
                            includeIndexClass: !1,
                            thresholdPercentage: 30,
                            thresholdOptions: { belowLabelClass: "ct-label-below", aboveLabelClass: "ct-label-above" },
                        },
                        h = { labelOffset: { x: 2, y: 4 }, textAnchor: "start" },
                        i = { labelOffset: { x: 0, y: -2 }, textAnchor: "middle" };
                    (f.plugins = f.plugins || {}),
                        (f.plugins.ctBarLabels = function (d) {
                            return function (e) {
                                if (e instanceof f.Bar) {
                                    (d = f.extend({}, g, d)), (d = e.options.horizontalBars ? f.extend({}, h, d) : f.extend({}, i, d));
                                    var j = a(e);
                                    e.on("draw", function (a) {
                                        if ("bar" === a.type) {
                                            var g = void 0 === a.value.x ? a.value.y : a.value.x,
                                                h = d.includeIndexClass ? ["ct-bar-label-i-", a.seriesIndex, "-", a.index].join("") : "",
                                                i = b(d.thresholdPercentage, d.thresholdOptions, j, g),
                                                k = c(d.labelPositionFnc, j, g, d.thresholdPercentage);
                                            (d = f.extend({}, d, k)),
                                                (d.showZeroLabels || (!d.showZeroLabels && 0 != g)) &&
                                                    a.group
                                                        .elem(
                                                            "text",
                                                            {
                                                                x: (d.startAtBase && e.options.horizontalBars ? a.x1 : a.x2) + d.labelOffset.x,
                                                                y: (d.startAtBase && !e.options.horizontalBars ? a.y1 : a.y2) + d.labelOffset.y,
                                                                style: `
                                                                    -webkit-writing-mode: vertical-lr;
                                                                    writing-mode: sideways-lr;
                                                                    -ms-writing-mode: vertical-lr;
                                                                    // text-anchor: ${d.textAnchor};
                                                                    // transform: rotate(-90deg);
                                                                    // transform-origin: ${d.startAtBase && e.options.horizontalBars ? 'left bottom' : 'left top'};
                                                                `,
                                                            },
                                                            [d.labelClass, h, i].join(" ")
                                                        )
                                                        .text(d.labelInterpolationFnc(g));
                                        }
                                    });
                                }
                            };
                        }),
                        (f.plugins.ctBarLabels.InsetLabelsPositionHorizontal = function (a) {
                            if (a.high && a.value && a.threshold) {
                                var b = (a.value / a.high) * 100 > a.threshold;
                                return b ? { labelOffset: { x: -2, y: 4 }, textAnchor: "end" } : { labelOffset: { x: 2, y: 4 }, textAnchor: "start" };
                            }
                        });
                })(window, document, Chartist),
                Chartist.plugins.ctBarLabels
            );
        }),
        Chartist.plugins.ctBarLabels
    );
});
