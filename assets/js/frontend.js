'use strict';



var $doc = document,
    btnSMData = {},
    $btnSMModal = null,
    $parentElement = null,
    $searchForm = null,
    $inputValue  = '',
    $ratingSelector = null;



// Document ready
var btnSM = function btnSM(callBack) {
  if ($doc.readyState !== 'loading') {
    callBack();
  } else if ($doc.addEventListener) {
    $doc.addEventListener('DOMContentLoaded', callBack);
  } else {
    $doc.attachEvent('onreadystatechange', function() {
      if ($doc.readyState === 'complete') {
        callBack();
      }
    });
  }
};

// Dom Ready
btnSM(function() {
  if (typeof btn_sm_data !== "undefined") {
      if (btn_sm_data.hasOwnProperty('btn_sm_shortcode')) {
          btnSMData = btn_sm_data.btn_sm_shortcode;
          var btnSMInit = btnSM(btnSMData);
          btnSMInit.init();

      }
  }

});

var btnSM = function(data) {
  var thisNewsletter;
  return {
    init: function() {
      thisNewsletter = this;
      thisNewsletter.pagination();
      thisNewsletter.selector();


   (function($) {
       var today = new Date();
        var currentDate = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
        var currentTime = today.getHours() + ":" + today.getMinutes();
       $('.timepicker').timepicker({
           timeFormat: "hh:mm tt",
           interval: 30,
           minTime: currentTime,
           dynamic: true,
           dropdown: true,
           scrollbar: true,
           onClose: function(value, inst) {
               if (value > ''){
                   var boost = $(this).attr('name'),
                       id = $(this).attr('data-id');

                   btnSMPreLoader('Loading...');
                   setTimeout(function(){
                     btnSMPost( {
                         action  : 'boost_action',
                         value : value,
   					  boost : boost,
                         id : id,
                     }, function(response){
   					  console.log(response);
                       if(response.success){
                           console.log(response);
                             $btnSMModal.remove();
                            location.reload();
                            //console.log(boost);
                       }
                     });
                   }, 300);
               }


           }
      });


    })(jQuery);
    },
    selector : function(){

      $doc.addEventListener('click', function(event) {
		  if(event.target.matches('.trigger-on-off')){
				var checked = (event.target.checked == true)? 1 : 0,
					data_id = event.target.getAttribute('data-id');
			   btnSMPreLoader('Loading...');

			    setTimeout(function(){
                  btnSMPost( {
                      action        : 'btn_sm_on_off_action',
                      checked : checked,
					  data_id : data_id,
                  }, function(response){
					  console.log(response);
                    if(response.success){

                          $btnSMModal.remove();

                    }
                  });
                }, 300);
		  }
      if(event.target.matches('.btn-sm-fav-inactive')){
          event.preventDefault();
          var id =  this.getAttribute("data-id");
              btnSMPreLoader('Loading...');
              setTimeout(function(){
                btnSMPost( {
                    action  : 'btn_sm_fav_action',
                    id : id,
                    favorite: "favorite",
                }, function(response){
                  if(response.success){
                      var all = $doc.querySelectorAll(".btn-sm-favourites-text");
                      for(var i = 0; i < all.length;i++){
                          all[i].classList.add("btn-sm-fav-active");
                          all[i].classList.remove("btn-sm-fav-inactive");
                      }
                      $btnSMModal.remove();
                  }
                });
              }, 300);
        }

        if(event.target.matches('.btn-np-search')){
            event.preventDefault();
            var topic =  $doc.querySelector(".btn-sm-selector-topic").value,
                volume = $doc.querySelector(".btn-sm-selector-volume").value,
                search = $doc.querySelector(".search-np").value;
                btnSMPreLoader('Loading...');

                setTimeout(function(){
                  btnSMPost( {
                      action        : 'btn_sm_action',
                      type          : 'selector',
                      posts_per_page: data.posts_per_page,
                      topic   : topic,
                      volume  : volume,
                      search : search,
                      page_url      : data.page_url
                  }, function(response){
                    if(response.success){
                      var $container = $doc.querySelector(".btn-sm-container"),
                        //  $selectorContainer = $doc.querySelector(".btn-ctc-toolbox-select-topic"),
                        html = response.data.html;
                        //selectorHtml = response.data.selector;
                        $container.innerHTML = html;
                        //window.history.pushState({"html":response.html,"pageTitle":response.pageTitle},"", pageURL);
                        $btnSMModal.remove();
                        console.log(response.data);

                    }
                  });
                }, 300);
          }
        if(event.target.matches('.remove-from-favorite')){
            event.preventDefault();
            var postId =  event.target.getAttribute("data-id");
                btnSMPreLoader('Loading...');
                setTimeout(function(){
                  btnSMPost( {
                      action        : 'btn_sm_remove_action',
                      posts_per_page: data.posts_per_page,
                      page_url      : data.page_url,
                      post_id: postId
                  }, function(response){
                    if(response.success){
                      var $container = $doc.querySelector("#btn-sm-wrapper-item-list-"+postId);
                        $btnSMModal.remove();
                        $container.remove();

                    }
                  });
                }, 300);
          }
      }, false);
    },
    pagination : function(){
      $doc.addEventListener('click', function(event) {
        if(event.target.matches('.page-numbers')){
            event.preventDefault();
            var pageURL =  event.target.getAttribute("href"),
                pageNum = pageURL.substr(pageURL.lastIndexOf('/') + 1),
                queryString = window.location.search,
                urlParams = new URLSearchParams(queryString);
                btnSMPreLoader('Loading...');
                setTimeout(function(){
                  btnSMPost( {
                      action        : 'btn_sm_action',
                      type          : 'pagination',
                      posts_per_page: data.posts_per_page,
                      paged         : pageNum,
                      page_url      : data.page_url
                  }, function(response){
                    if(response.success){
                      var $container = $doc.querySelector(".btn-sm-container"),
                        //  $selectorContainer = $doc.querySelector(".btn-ctc-toolbox-select-topic"),
                        html = response.data.html;
                        //selectorHtml = response.data.selector;
                        $container.innerHTML = html;
                        //window.history.pushState({"html":response.html,"pageTitle":response.pageTitle},"", pageURL);
                        $btnSMModal.remove();
                    }
                  });
                }, 300);
          }
      }, false);
    },
  }
};


var btnSMPreLoader = function( message ){
    if( $btnSMModal != null ){
        $btnSMModal.remove();
    }
    var html = '<div id="btn_sm_preloader_wrap" class="btn_sm_overlay">';
    html += '<div class="btn_sm_preloader"></div>';
    if( message > '' ){
        html += '<div class="btn_sm_preloader_msg">'+message+'</div>';
    }
    html += '</div>';
    $doc.body.insertAdjacentHTML('beforeend', html);
    $btnSMModal = $doc.querySelector('#btn_sm_preloader_wrap');
};


var btnSMPost = function( postData, callback ){
    var request = new XMLHttpRequest();
    var encodedData = Object.keys(postData).map(function(key) {
        return key + '=' + encodeURIComponent(postData[key])
    }).join('&');
    request.open('POST', btn_sm_data.ajax_url, false);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    request.onload = function () {
        // Process the response
        if (request.status >= 200 && request.status < 300) {
            var response = false;
            try {
                response = JSON.parse(request.responseText);
            }
            catch (err) {
                response = false;
            }
            callback(response);
        }
        else{
            console.log({
                func: 'btnSMPost',
                status: request.status,
                statusText: request.statusText
            });
        }
    }
    request.send(encodedData);
};


//Closest Polyfill
window.Element && !Element.prototype.closest && (Element.prototype.closest = function(e) {
  var t, o = (this.document || this.ownerDocument).querySelectorAll(e),
    n = this;
  do {
    for (t = o.length; --t >= 0 && o.item(t) !== n;);
  } while (t < 0 && (n = n.parentElement));
  return n
});



!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).MicroModal=t()}(this,function(){"use strict";return(()=>{const e=["a[href]","area[href]",'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',"select:not([disabled]):not([aria-hidden])","textarea:not([disabled]):not([aria-hidden])","button:not([disabled]):not([aria-hidden])","iframe","object","embed","[contenteditable]",'[tabindex]:not([tabindex^="-"])'];class t{constructor({targetModal:e,triggers:t=[],onShow:o=(()=>{}),onClose:i=(()=>{}),openTrigger:n="data-micromodal-trigger",closeTrigger:s="data-micromodal-close",disableScroll:a=!1,disableFocus:l=!1,awaitCloseAnimation:d=!1,awaitOpenAnimation:r=!1,debugMode:c=!1}){this.modal=document.getElementById(e),this.config={debugMode:c,disableScroll:a,openTrigger:n,closeTrigger:s,onShow:o,onClose:i,awaitCloseAnimation:d,awaitOpenAnimation:r,disableFocus:l},t.length>0&&this.registerTriggers(...t),this.onClick=this.onClick.bind(this),this.onKeydown=this.onKeydown.bind(this)}registerTriggers(...e){e.filter(Boolean).forEach(e=>{e.addEventListener("click",e=>this.showModal(e))})}showModal(){if(this.activeElement=document.activeElement,this.modal.setAttribute("aria-hidden","false"),this.modal.classList.add("is-open"),this.scrollBehaviour("disable"),this.addEventListeners(),this.config.awaitOpenAnimation){const e=()=>{this.modal.removeEventListener("animationend",e,!1),this.setFocusToFirstNode()};this.modal.addEventListener("animationend",e,!1)}else this.setFocusToFirstNode();this.config.onShow(this.modal,this.activeElement)}closeModal(){const e=this.modal;this.modal.setAttribute("aria-hidden","true"),this.removeEventListeners(),this.scrollBehaviour("enable"),this.activeElement&&this.activeElement.focus(),this.config.onClose(this.modal),this.config.awaitCloseAnimation?this.modal.addEventListener("animationend",function t(){e.classList.remove("is-open"),e.removeEventListener("animationend",t,!1)},!1):e.classList.remove("is-open")}closeModalById(e){this.modal=document.getElementById(e),this.modal&&this.closeModal()}scrollBehaviour(e){if(!this.config.disableScroll)return;const t=document.querySelector("body");switch(e){case"enable":Object.assign(t.style,{overflow:"",height:""});break;case"disable":Object.assign(t.style,{overflow:"hidden",height:"100vh"})}}addEventListeners(){this.modal.addEventListener("touchstart",this.onClick),this.modal.addEventListener("click",this.onClick),document.addEventListener("keydown",this.onKeydown)}removeEventListeners(){this.modal.removeEventListener("touchstart",this.onClick),this.modal.removeEventListener("click",this.onClick),document.removeEventListener("keydown",this.onKeydown)}onClick(e){e.target.hasAttribute(this.config.closeTrigger)&&(this.closeModal(),e.preventDefault())}onKeydown(e){27===e.keyCode&&this.closeModal(e),9===e.keyCode&&this.maintainFocus(e)}getFocusableNodes(){const t=this.modal.querySelectorAll(e);return Array(...t)}setFocusToFirstNode(){if(this.config.disableFocus)return;const e=this.getFocusableNodes();e.length&&e[0].focus()}maintainFocus(e){const t=this.getFocusableNodes();if(this.modal.contains(document.activeElement)){const o=t.indexOf(document.activeElement);e.shiftKey&&0===o&&(t[t.length-1].focus(),e.preventDefault()),e.shiftKey||o!==t.length-1||(t[0].focus(),e.preventDefault())}else t[0].focus()}}let o=null;const i=e=>{if(!document.getElementById(e))return console.warn(`MicroModal: ❗Seems like you have missed %c'${e}'`,"background-color: #f8f9fa;color: #50596c;font-weight: bold;","ID somewhere in your code. Refer example below to resolve it."),console.warn("%cExample:","background-color: #f8f9fa;color: #50596c;font-weight: bold;",`<div class="modal" id="${e}"></div>`),!1},n=(e,t)=>{if((e=>{if(e.length<=0)console.warn("MicroModal: ❗Please specify at least one %c'micromodal-trigger'","background-color: #f8f9fa;color: #50596c;font-weight: bold;","data attribute."),console.warn("%cExample:","background-color: #f8f9fa;color: #50596c;font-weight: bold;",'<a href="#" data-micromodal-trigger="my-modal"></a>')})(e),!t)return!0;for(var o in t)i(o);return!0};return{init:e=>{const i=Object.assign({},{openTrigger:"data-micromodal-trigger"},e),s=[...document.querySelectorAll(`[${i.openTrigger}]`)],a=((e,t)=>{const o=[];return e.forEach(e=>{const i=e.attributes[t].value;void 0===o[i]&&(o[i]=[]),o[i].push(e)}),o})(s,i.openTrigger);if(!0!==i.debugMode||!1!==n(s,a))for(var l in a){let e=a[l];i.targetModal=l,i.triggers=[...e],o=new t(i)}},show:(e,n)=>{const s=n||{};s.targetModal=e,!0===s.debugMode&&!1===i(e)||(o=new t(s)).showModal()},close:e=>{e?o.closeModalById(e):o.closeModal()}}})()});

