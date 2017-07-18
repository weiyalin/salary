Vue.component('remote-script', {

    render: function (createElement) {
        var self = this;
        return createElement('script', {
            attrs: {
                type: 'text/javascript',
                src: this.src
            },
            on: {
                load: function (event) {
                    self.$emit('load', event);
                },
                error: function (event) {
                    self.$emit('error', event);
                },
                readystatechange: function (event) {
                    if (this.readyState == 'complete') {
                        self.$emit('load', event);
                    }
                }
            }
        });
    },

    props: {
        src: {
            type: String,
            required: true
        }
    }
});

Vue.component('remote-link', {

    render: function (createElement) {
        return createElement('link', {
            attrs: {
                rel: 'stylesheet',
                href: this.href
            }
        });
    },

    props: {
        href: {
            type: String,
            required: true
        }
    }
});