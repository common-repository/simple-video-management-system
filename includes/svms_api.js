
var svms_players = {};

function svms_video(id) {
	if (svms_players[id]) {
		return svms_players[id];
	}

	svms_players[id] = new svms_player(id);

	return svms_players[id];
};

function svms_swap_video(id, handle) {
	var player = svms_video(id);

	jQuery('#vpp_watch_more_' + player.handle).remove();

	player.changeVideo(handle);
}

function svms_player(id) {

	this.id = id;
	this.handle = id.replace('vpp_video_', '');

	this.opts = {
		techOrder: [
			'html5',
			'flash',	
			'youtube'
		],
		width: 'auto',
		height: 'auto'
	};

	this.config = {};

	this.changeVideo = function(handle) {
		this.handle = handle.replace('vpp_video_', '');
		this.init();
	};

	this.loadVideo = function() {
		jQuery.ajax({
			url: svms_ajax_url + '?action=s3_video_player_plus&do=api&method=getVideoConfig&handle=' + this.handle,
			dataType: 'json',
			context: this
		}).done(function(config){
			this.applyConfig(config);
		});
	};

	this.applyConfig = function(config) {
		this.config = config;

		this.player.controls(config.controls);
		this.player.autoplay(config.autoplay);
		this.player.loop(config.loop);
		this.player.preload(config.preload);
		this.player.width(config.width);
		this.player.height(config.height);

		this.player.src(config.src);

		if (config.poster) this.player.poster = config.poster;

		if (config.redirect_url)
		{
			this.player.on('ended', function(e){
				window.location.href = config.redirect_url;
			}, false);
		}
		else if (config.has_watch_more)
		{
			this.player.on('ended', function(e){
				svms_video(e.target.player.K).loadWatchMore();
			}, false);
		}

		if (config.google_analytics)
		{
			this.initGAnalytics();
		}

		if (config.show_html_seconds > 0)
		{
			this.player.on('timeupdate', function(e){
				if (this.currentTime() >= config.show_html_seconds)
				{
					jQuery('#vpp_show_html_' + this.id).show(1000);
				}
			}, false);
		}

		this.updateSize();
	};

	this.loadWatchMore = function(){
		jQuery('#vpp_watch_more_wrapper_' + this.handle).load(svms_ajax_url + '?action=s3_video_player_plus&do=api&method=getWatchMore&id=' + this.id + '&handle=' + this.handle);
	};

	// TODO: connect and test this!!!
	this.initGAnalytics = function(){
		if (typeof ga != 'undefined')
		{
			this.player.on('play', function(e){
				ga('send', 'event', 'Videos', 'Play', this.config.title);
			}, false);

			this.player.on('pause', function(e){
				ga('send', 'event', 'Videos', 'Pause', this.config.title);
			}, false);

			this.player.on('ended', function(e){
				ga('send', 'event', 'Videos', 'Ended', this.config.title);
			}, false);
		}
	};
	
	this.updateSize = function(){
		this.player.height(this.player.width() * this.config.aspect_ratio);
	};

	// Init
	this.init = function() {
		this.player = videojs(this.id, this.opts).ready(this.loadVideo());	
	};

	this.init();
}

jQuery(document).ready(function() {
	jQuery('.vpp_video').each(function(){
		svms_video(jQuery(this).attr('id'));
	});

	jQuery(window).resize(function() {
		jQuery('.vpp_video').each(function(){
			var video = svms_video(jQuery(this).attr('id'));
			video.updateSize();
		})
	});
});
