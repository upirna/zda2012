dojo.require("dojo.parser");



dojo.provide("application.page.Homepage");
dojo.declare(
	"application.page.Homepage",
	null,
	{
		/**
		 * Constructor
		 */
		constructor: function() 
		{
			dojo.connect(dojo.byId('videoblog'), 'onmouseover', this, 'setVideblogHoverImage');
			dojo.connect(dojo.byId('videoblog'), 'onmouseout', this, 'setVideblogImage');
			
			dojo.connect(dojo.byId('blog'), 'onmouseover', this, 'setBlogHoverImage');
			dojo.connect(dojo.byId('blog'), 'onmouseout', this, 'setBlogImage');			
			
			dojo.connect(dojo.byId('gallery'), 'onmouseover', this, 'setGalleryHoverImage');
			dojo.connect(dojo.byId('gallery'), 'onmouseout', this, 'setGalleryImage');
 
            
		},
		
		
		/**
		 * Chnage video blog image
		 */
		setVideblogHoverImage: function(event)
		{
			dojo.byId('videoblog').src = baseUrl + '/images/zda2012/videodnevnik-hover.png';
		},
		
		
		/**
		 * Chnage video blog image
		 */
		setVideblogImage: function(event)
		{
			dojo.byId('videoblog').src = baseUrl + '/images/zda2012/videodnevnik.png';
		},		
		
		/**
		 * Chnage blog image
		 */
		setBlogHoverImage: function(event)
		{
			dojo.byId('blog').src = baseUrl + '/images/zda2012/blog-hover.png';
		},
		
		
		/**
		 * Chnage blog image
		 */
		setBlogImage: function(event)
		{
			dojo.byId('blog').src = baseUrl + '/images/zda2012/blog.png';
		},	
		
		/**
		 * Chnage gallery image
		 */
		setGalleryHoverImage: function(event)
		{
			dojo.byId('gallery').src = baseUrl + '/images/zda2012/gallery-hover.png';
		},
		
		
		/**
		 * Chnage gallery image
		 */
		setGalleryImage: function(event)
		{
			dojo.byId('gallery').src = baseUrl + '/images/zda2012/gallery.png';
		},			
		
		
		
	}
);


