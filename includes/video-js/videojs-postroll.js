/*! videojs-postroll - v1.1.0 - 2016-10-19
* Copyright (c) 2015 Sano Webdevelopment;
* Copyright (c) 2014 The Onion
* Licensed MIT */
(function(window, videojs) {
  'use strict';

  var defaults = {
    src : '', //Advertisement source, can also be an object like {src:"file.mp4",type:"video/mp4"}
    href : '', //Advertised url
    target: '_blank', //Target to open the ad url in
    allowSkip: false, //Allow skipping of the ad after a certain period
    skipTime: 5, //Seconds after which the ad can be skipped
    repeatAd: false, //Show the ad only once or after every conten
    adSign: false, //Advertisement sign
    showRemaining: false, //Show remaining ad time > works if allowSkip is false
    adsOptions: {}, //Options passed to the ads plugin
    lang: {
      'skip':'Skip',
      'skip in': 'Skip in ',
      'advertisement': 'Advertisement',
      'video start in': 'Video will start in: '
    } //Language entries for translation
  }, postrollPlugin;

  //
  // Initialize the plugin.
  //
  // @param options
  //            (optional) {object} configuration for the plugin
  //
  postrollPlugin = function(options) {
    var settings = videojs.mergeOptions(defaults, options), player = this;
   // player.ads(settings.adsOptions);
    player.postroll = {adDone:false};       
    player.on('contentupdate', function() {
      if(!player.postroll.shouldPlayPostroll()){
        player.trigger('adscanceled');
      }else{
        player.trigger('adsready');
      }
    });
    player.on('readyforpostroll', function() {
      // No video? No ad.
      if(!player.postroll.shouldPlayPostroll()){
        player.trigger('adscanceled');
        return;
      }

      // Initialize ad mode
      player.ads.startLinearAdMode();

      // Change player src to ad src
      player.src(settings.src);
      player.one('durationchange', function() {
        player.play();
      });

      //Fallback in case preload = none
      player.one('progress', function() {
        player.play();
      });
      player.one('adloadstart',function(){
        player.play();
      });

      if(settings.href !== ''){
        // link overlay
        var blocker = document.createElement('a');
        blocker.className = 'postroll-blocker';
        blocker.href = settings.href;
        blocker.target = settings.target || '_blank';
        blocker.onclick = function() {
          player.trigger('adclick');
        };
        player.postroll.blocker = blocker;
        player.el().insertBefore(blocker, player.controlBar.el());
      }

      if(settings.adSign !== false){
        var adBox = document.createElement('div');
        adBox.className = 'advertisement-box';
        player.postroll.adBox = adBox;
        player.el().appendChild(adBox);
        player.postroll.adBox.innerHTML = settings.lang.advertisement;
      }

      if(settings.showRemaining !== false && settings.allowSkip === false){
        var remainingTime = document.createElement('div');
        remainingTime.className = 'remaining-time';
        player.postroll.remainingTime = remainingTime;
        player.el().appendChild(remainingTime);
        player.postroll.remainingTime.innerHTML = settings.lang['video start in'];
        player.on('adtimeupdate', player.postroll.timeremaining);
      }

      if (settings.allowSkip !== false){
        var skipButton = document.createElement('div');
        skipButton.className = 'postroll-skip-button';
        player.postroll.skipButton = skipButton;
        player.el().appendChild(skipButton);

        skipButton.onclick = function(e) {
          var Event = Event || window.Event;
          if((' ' + player.postroll.skipButton.className + ' ').indexOf(' enabled ') >= 0) {
            player.postroll.exitPreroll();
          }
          if(Event.prototype.stopPropagation !== undefined) {
            e.stopPropagation();
          } else {
            return false;
          }
        };
        player.on('adtimeupdate', player.postroll.timeupdate);
      }
      player.one('adended', player.postroll.exitPreroll);
      player.one('error', player.postroll.postrollError);
    });
    player.postroll.shouldPlayPostroll = function(){
      if (settings.src === ''){
        return false;
      }
      if (player.postroll.adDone === true){
        return false;
      }
      return true;
    };
    player.postroll.exitPreroll = function() {
      if(typeof player.postroll.skipButton !== 'undefined'){
        player.postroll.skipButton.parentNode.removeChild(player.postroll.skipButton);
      }
      if(typeof player.postroll.adBox !== 'undefined'){
        player.postroll.adBox.parentNode.removeChild(player.postroll.adBox);
      }
      if(typeof player.postroll.remainingTime !== 'undefined'){
        player.postroll.remainingTime.parentNode.removeChild(player.postroll.remainingTime);
      }
      if(typeof player.postroll.blocker !== 'undefined'){
        player.postroll.blocker.parentNode.removeChild(player.postroll.blocker);
      }
      //player.off('timeupdate', player.postroll.timeupdate);
      player.off('adended', player.postroll.exitPreroll);
      player.off('error', player.postroll.postrollError);
      if (settings.repeatAd !== true){
        player.postroll.adDone=true;
      }
      player.loadingSpinner.show(); //Show Spinner to provide feedback of video loading status to user
      player.posterImage.hide(); //Hide Poster Image to provide feedback of video loading status to user
      player.bigPlayButton.hide(); //Hide Play Button to provide feedback of video loading status to user
      player.ads.endLinearAdMode();
    };
    player.postroll.timeupdate = function(e) {
      player.loadingSpinner.hide();
      var timeLeft = Math.ceil(settings.skipTime - player.currentTime());
      if(timeLeft > 0) {
        player.postroll.skipButton.innerHTML = settings.lang['skip in'] + timeLeft + '...';
      } else {
        if((' ' + player.postroll.skipButton.className + ' ').indexOf(' enabled ') === -1){
          player.postroll.skipButton.className += ' enabled';
          player.postroll.skipButton.innerHTML = settings.lang.skip;
        }
      }
    };
    player.postroll.timeremaining = function(e) {
      player.loadingSpinner.hide();
      var timeLeft = Math.ceil(player.remainingTime());
      if(timeLeft > 0) {
        player.postroll.remainingTime.innerHTML = settings.lang['video start in'] + timeLeft;
      }
    };
    player.postroll.postrollError = function(e){
      player.postroll.exitPreroll();
    };
    if (player.currentSrc()) {
      if(player.postroll.shouldPlayPostroll()){
        player.trigger('adsready');
      }else{
        player.trigger('adscanceled');
      }
    }
  };

  // Register the plugin (cross-compatibility for Video.js 5 and 6)
  var registerPlugin = videojs.registerPlugin || videojs.plugin;
  registerPlugin('postroll', postrollPlugin);

})(window, window.videojs);
