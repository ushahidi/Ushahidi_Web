var process = function (impact_json) {

    var x = 0; // left margin
    var width = (impact_json.buckets.length * 100);
    var height = "99%";
    var r = Raphael("impact_chart", width, height);
    var labels = {};
    var textattr = {"font": '9px "Arial"', stroke: "none", fill: "#fff"};
    var pathes = {};
    var nmhldr = $("#impact_info")[0];
    var nmhldr2 = $("#impact_info2")[0];
    var lgnd = $("#impact_legend")[0];
    var usrnm = $("#impact_message")[0];
    var lgnd2 = $("#impact_legend2")[0];
    var usrnm2 = $("#impact_message2")[0];
    var plchldr = $("#impact_placeholder")[0];
    
    function finishes() {
        for (var i in impact_json.categories) {
            var start, end;
            for (var j = impact_json.buckets.length - 1; j >= 0; j--) {
                var isin = false;
                for (var k = 0, kk = impact_json.buckets[j].i.length; k < kk; k++) {
                    isin = isin || (impact_json.buckets[j].i[k][0] == i);
                }
                if (isin) {
                    end = j;
                    break;
                }
            }
            for (var j = 0, jj = impact_json.buckets.length; j < jj; j++) {
                var isin = false;
                for (var k = 0, kk = impact_json.buckets[j].i.length; k < kk; k++) {
                    isin = isin || (impact_json.buckets[j].i[k][0] == i);
                };
                if (isin) {
                    start = j;
                    break;
                }
            }
            for (var j = start, jj = end; j < jj; j++) {
                var isin = false;
                for (var k = 0, kk = impact_json.buckets[j].i.length; k < kk; k++) {
                    isin = isin || (impact_json.buckets[j].i[k][0] == i);
                }
                if (!isin) {
                    impact_json.buckets[j].i.push([i, 0]);
                }
            }
        }
    }
    function block() {
        var p, h;
        finishes();
        for (var j = 0, jj = impact_json.buckets.length; j < jj; j++) {
            var category_count = impact_json.buckets[j].i;
            var category = 0;
            var count = 0;
            h = 0;
            for (var i = 0, ii = category_count.length; i < ii; i++) {
            	category = category_count[i][0];
            	count = category_count[i][1];
            	// This makes things nice and fat
            	modified_count = count * 2;
                p = pathes[category];
                if (!p) {
                    p = pathes[category] = {f:[], b:[]};
                }
                p.f.push([x, h, count]);
                
                if(impact_json.use_log == 1) {
                	p.b.unshift([x, h += Math.max(Math.round(Math.log(modified_count) * 5), 1)]);
                }else{
                	p.b.unshift([x, h += Math.max(Math.round(modified_count * 3), 1)]);
                }
                
                h += 2;
            }
            var dt = new Date(impact_json.buckets[j].d * 1000);
            var dtext = dt.getDate() + " " + ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"][dt.getMonth()] + " " + dt.getFullYear();
            r.text(x + 25, h + 10, dtext).attr({"font": '9px "Arial"', stroke: "none", fill: "#aaa"});
            x += 100;
        }
        var c = 0;
        for (var i in pathes) {
            labels[i] = r.set();
            var clr = impact_json.categories[i].fill;
            pathes[i].p = r.path().attr({fill: clr, stroke: clr});
            var path = "M".concat(pathes[i].f[0][0], ",", pathes[i].f[0][1], "L", pathes[i].f[0][0] + 50, ",", pathes[i].f[0][1]);
            var th = Math.round(pathes[i].f[0][1] + (pathes[i].b[pathes[i].b.length - 1][1] - pathes[i].f[0][1]) / 2 + 3);
            labels[i].push(r.text(pathes[i].f[0][0] + 25, th, pathes[i].f[0][2]).attr(textattr));
            var X = pathes[i].f[0][0] + 50,
                Y = pathes[i].f[0][1];
            for (var j = 1, jj = pathes[i].f.length; j < jj; j++) {
                path = path.concat("C", X + 20, ",", Y, ",");
                X = pathes[i].f[j][0];
                Y = pathes[i].f[j][1];
                path = path.concat(X - 20, ",", Y, ",", X, ",", Y, "L", X += 50, ",", Y);
                th = Math.round(Y + (pathes[i].b[pathes[i].b.length - 1 - j][1] - Y) / 2 + 3);
                if (th - 9 > Y) {
                    labels[i].push(r.text(X - 25, th, pathes[i].f[j][2]).attr(textattr));
                }
            }
            path = path.concat("L", pathes[i].b[0][0] + 50, ",", pathes[i].b[0][1], ",", pathes[i].b[0][0], ",", pathes[i].b[0][1]);
            for (var j = 1, jj = pathes[i].b.length; j < jj; j++) {
                path = path.concat("C", pathes[i].b[j][0] + 70, ",", pathes[i].b[j - 1][1], ",", pathes[i].b[j][0] + 70, ",", pathes[i].b[j][1], ",", pathes[i].b[j][0] + 50, ",", pathes[i].b[j][1], "L", pathes[i].b[j][0], ",", pathes[i].b[j][1]);
            }
            pathes[i].p.attr({path: path + "z"});
            labels[i].hide();
            var current = null;
            (function (i) {
                pathes[i].p.mouseover(function () {
                    if (current != null) {
                        labels[current].hide();
                    }
                    current = i;
                    labels[i].show();
                    pathes[i].p.toFront();
                    labels[i].toFront();
                    usrnm2.innerHTML = impact_json.categories[i].name + " <em>(" + impact_json.categories[i].reports + " Reports)</em>";
                    lgnd2.style.backgroundColor = pathes[i].p.attr("fill");
                    nmhldr2.className = "";
                    plchldr.className = "impact_hidden";
                });
            })(i);
        }
    }
    if (impact_json.error) {
        alert("JSON Error. Try again.");
    } else {
        block();
    }
};

$(function () {
    process(impact_json);
});