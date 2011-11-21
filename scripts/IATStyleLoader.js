define(["jquery","CoreIAT"],function($,CoreIAT) {
  return {
    loadStyle : function (style) {
      var deferred = $.Deferred();
      var html = 'text!../IATStyles/' + style + '/' + style + '.html';
      var css = 'text!../IATStyles/' + style + '/' + style + '.css';
      require([html,css],function (styleHtml,css) {
        deferred.loadedStyle = $(styleHtml).append($('<style>').append(css));
        deferred.resolve();
      });
      return deferred;
    },
    randomizeArray : function (array) {
      var randomArray = [];
      while (array.length > 0) {
        var idx = Math.floor(Math.random() * array.length);
        randomArray.push(array.slice(idx, idx));
      }
    }
  }
});
