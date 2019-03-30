// loading Vue w/ bower instead of browserify
// b/c there were some npm module issues when using browserify require(...)
var Vue = global.Vue;
    VueStrap = global.VueStrap;

Vue.directive('select2', {
  twoWay: true,
  priority: 1000,

  params: ['options'],
    
  bind: function () {
    var self = this
    $(this.el)
      .select2({
        data: this.params.options
      })
      .on('change', function () {
        self.set(this.value)
      })
  },
  update: function (value) {
    $(this.el).val(value).trigger('change')
  },
  unbind: function () {
    $(this.el).off().select2('destroy')
  }
});

Vue.component('vue-strap-alert', VueStrap.alert);
Vue.component('vue-strap-carousel', VueStrap.carousel);
Vue.component('vue-strap-slider', VueStrap.slider);
Vue.component('vue-strap-accordion', VueStrap.accordion);
Vue.component('vue-strap-aside', VueStrap.aside);
Vue.component('vue-strap-checkboxGroup', VueStrap.checkboxGroup);
Vue.component('vue-strap-checkboxBtn', VueStrap.checkboxBtn);
Vue.component('vue-strap-datepicker', VueStrap.datepicker);
Vue.component('vue-strap-dropdown', VueStrap.dropdown);
Vue.component('vue-strap-modal', VueStrap.modal);
Vue.component('vue-strap-panel', VueStrap.panel);
Vue.component('vue-strap-popover', VueStrap.popover);
Vue.component('vue-strap-progressbar', VueStrap.progressbar);
Vue.component('vue-strap-radioBtn', VueStrap.radioBtn);
Vue.component('vue-strap-radioGroup', VueStrap.radioGroup);
Vue.component('vue-strap-select', VueStrap.select);
Vue.component('vue-strap-tab', VueStrap.tab);
Vue.component('vue-strap-tabset', VueStrap.tabset);
Vue.component('vue-strap-tooltip', VueStrap.tooltip);
Vue.component('vue-strap-typeahead', VueStrap.typeahead);

Vue.component('content-upload-button', Vue.extend(require('./components/UploadButton.vue')));
Vue.component('content-modal-image-browser', Vue.extend(require('./components/ModalImageBrowser.vue')));
Vue.component('content-image-gallery', Vue.extend(require('./components/ImageGalleryComponent.vue')));
Vue.component('content-file-list', Vue.extend(require('./components/FileListComponent.vue')));
Vue.component('content-image', Vue.extend(require('./components/ImageComponent.vue')));
Vue.component('content-image-grid', Vue.extend(require('./components/ImageGrid.vue')));
Vue.component('content-file-grid', Vue.extend(require('./components/FileGrid.vue')));


global.Vue = Vue;
for (var key in Vue.options.components) {
    var camelCased = key.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
    camelCased = camelCased.charAt(0).toUpperCase() + camelCased.slice(1);
    global[camelCased] = Vue.options.components[key];
}

jQuery(function() {
    jQuery('.ImageGalleryComponent').each(function() {
        var element = jQuery(this),
            data = {
                images: element.find('.Image').map(function() {
                    return jQuery(this).data();
                }).toArray()
            };
        jQuery.each(element.data(), function(k, v) {
            data[k] = v;
            element.removeAttr(k);
        });
        element.children().remove();
        new global.ContentImageGallery({
            'el': element[0],
            'data': data
        });
    });
});

jQuery(function() {
    jQuery('.FileListComponent').each(function() {
        var element = jQuery(this),
            data = {
                files: element.find('.File').map(function() {
                    return jQuery(this).data();
                }).toArray()
            };
        jQuery.each(element.data(), function(k, v) {
            data[k] = v;
            element.removeAttr(k);
        });
        element.children().remove();
        new global.ContentFileList({
            'el': element[0],
            'data': data
        });
    });
});

jQuery(function() {
    jQuery('.ImageComponent').each(function() {
        var element = jQuery(this),
            data = {
                current_image: element.find('.CurrentImage').data()
            };
        jQuery.each(element.data(), function(k, v) {
            data[k] = v;
            element.removeAttr(k);
        });
        element.children().remove();
        new global.ContentImage({
            'el': element[0],
            'data': data
        });
    });
});

jQuery(function() {
    jQuery('.ckeditor-textarea').each(function() {
        var el = jQuery(this),
            options = {
                customConfig: '/vendor/content/ckeditor_config.js',
                reflexions: { }
            };
        jQuery.each(el.data(), function(k, v) {
            options.reflexions[k] = v;
        });
        options.filebrowserBrowseUrl = options.reflexions.ckeditor_file_browser;
        options.filebrowserImageBrowseUrl = options.reflexions.ckeditor_image_browser;
        CKEDITOR.replace( el.attr('id'), options );
    });
});
