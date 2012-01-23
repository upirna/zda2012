dojo.require("dojo.parser");



dojo.provide("application.page.Team");
dojo.declare(
	"application.page.Team",
	null,
	{
		/**
		 * Constructor
		 */
		constructor: function() 
		{
			var showMoreLinks = dojo.query('.showMoreLink');
			
			dojo.forEach(showMoreLinks, dojo.hitch(this, function(link) {
				dojo.connect(link, 'onclick', this, 'showOrHideMore');
			}));
			
		},
		
		
		/**
		 * Show or hide more
		 */
		showOrHideMore: function(event)
		{
			
			var id = event.currentTarget.id.replace('link-', '');
			var currentStatus = dojo.byId('details-' + id).style.display;
			
			if (currentStatus == 'none') {
				dojo.byId('details-' + id).style.display = 'block'; 
				event.currentTarget.innerHTML = 'Skrij podrobnosti';
			} else {
				dojo.byId('details-' + id).style.display = 'none';
				event.currentTarget.innerHTML = 'Prikaži podrobnosti';
			}
			
			
			
		}
		
		
		
	}
);


