/*!
 * WebIAT
 * This project was originally developed for Margaret Shih and Geoff Ho at the
 * UCLA Anderson School of Management by Stephen Searles.
 * This code is licensed under the Eclipse Public License (EPL) version 1.0,
 * which is available here: http://www.eclipse.org/legal/epl-v10.html.
 *
 * ClientIATManager.js
 *
 * This file serves as the client-side engine behind WebIAT. It generates all
 * the HTML, produces all the effects, and handles all the communication with
 * the server.
 *
 * Author: Stephen Searles
 * Date: May 10, 2011
 */

/*
 * Supplies Object.create in the global context. For an explanation, see Douglas
 * Crockford: http://javascript.crockford.com/prototypal.html
 */
if (typeof Object.create !== 'function') {
  Object.create = function (o) {
    function F() {}
    F.prototype = o;
    return new F();
  };
}

/*
 * Extends jQuery to allow it to get variables passed through the URL.
 * Source: http://jquery-howto.blogspot.com/2009/09/get-url-parameters-values-with-jquery.html
 */
$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
  }
});

/*
 * This function wraps the entire WebIAT to provide a hidden local context and
 * modularity.
 */
define(["../configuration/config"],function (config) {
  return $.extend({
    /*
     * Bundles an IAT server-side request with data. requestName must be a valid
     * function name for the server side.
     */
    bundleIATManagerRequestData : function(requestName, dataObject) {
      return {
        "requestName":requestName,
        "data":dataObject
      };
    },

    /*
     * Sends a bundled request to the server. See 'bundleIATManagerRequestData'.
     * sendRequest handles general errors returned by the server-side, such as
     * invalid authentication or insufficient permission.
     *
     * sendRequest wraps all requests in a deferred object and returns that
     * immediately.
     */
    sendRequest : function(requestObject,recursion) {
      var deferred = $.Deferred();
      if (!recursion) recursion = 0;
      else if (recursion > 3) return undefined;
      $.post(config.managerFilePath,requestObject).done(function (receivedData,textStatus,jqXHR) {
        var data = JSON.parse(receivedData);
        if (data && (data.errorCode === '1003' || data.errorCode == '1004')) {
          $.jnotify("You do not have sufficient permission for the attempted function.");
        } else {
          deferred.resolveWith(this,[data]);
        }
      });
      return deferred;
    },

    /*
     * Sends a synchronous request to the server and returns the raw result. It
     * does not handle any errors and should be used minimally.
     */
    sendSynchronousRequest : function(requestObject) {
      return $.ajax(config.managerFilePath,{
        async: false,
        data: requestObject,
        type: "POST"
      });
    }
  },config);
});
