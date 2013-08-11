(function($) {
    "use strict";
    
    var Obj = function() {
        this.expireEl = $('#cache_expire');
        this.humanEl = $('#human-time');
        this.expireSeconds = parseInt(this.expireEl.val());
        
        this.init();
    };
    
    Obj.prototype.init = function() {
        this.registerHandlers();
        this.updateHumanTime();
    };
    
    Obj.prototype.registerHandlers = function() {
        this.expireEl.on('keyup', $.proxy(this.validateCacheExpire, this));
    };
    
    Obj.prototype.validateCacheExpire = function(event) {        
        event = event || {};
        var blacklisted = [37, 38, 39, 40];
        if (!_.isUndefined(event.keyCode) && $.inArray(event.keyCode, blacklisted) === -1) {
            this.humanEl.text("");
            this.expireSeconds = parseInt(this.expireEl.val());
            if (!_.isNumber(this.expireSeconds) || isNaN(this.expireSeconds) && this.expireEl.val() !== "") {
                this.expireEl.val("");
                this.humanEl.text(SS.invalid_expire_value);
                
                return;
            }
            else if(this.expireEl.val() === "") {
                this.expireEl.val("0");
                this.expireSeconds = parseInt(this.expireEl.val());
            }
            this.updateHumanTime();
        }
    };
    
    Obj.prototype.updateHumanTime = function() {
        this.expireEl.val(this.expireSeconds);
        if (this.expireSeconds === 0) {
            this.humanEl.text(SS.cache_disabled);
        }
        else {
            // calculate
            var hours = (this.expireSeconds / 60 / 60).toFixed(1);
            this.humanEl.text(hours + ' ' + SS.hours);
        }
    };
    
    $(document).ready(function() {
        SS.obj = new Obj();
    });
    
}(jQuery));