function htmlbox_undo_redo_manager(repeat) {
    var r = false;
    var d = [];
    var p = 0;
    this.d = function () {
        return d;
    };
    this.add = function (o) {
        if (d[p - 1] == o) return false;
        d[p] = o;
        p++;
        d = d.slice(0, p);
    };
    this.can_redo = function () {
        if (p >= d.length) {
            return false;
        }
        return true;
    };
    this.can_undo = function () {
        if (p < 1) {
            return false;
        }
        return true;
    };
    this.clear = function () {
        d = [];
        p = 0;
    };
    this.undo = function () {
        if (p < 1) {
            return false;
        }
        p--;
        return d[p - 1];
    };
    this.redo = function () {
        if (p >= d.length) {
            return false;
        }
        p++;
        return d[p - 1];
    };
}