( function( $ ) {

    var CodeSettings = {

        currentNodeId: null,

        init: function() {
            FLBuilder.addHook( 'settings-form-init', CodeSettings.settingsFormInit );
			FLBuilder.addHook( 'didSaveNodeSettingsComplete', CodeSettings.clearPreview );
        },

        clearPreview: function() {
			$( 'style.fl-builder-node-preview' ).remove();
        },

        settingsFormInit: function() {
            var style = $( 'style.fl-builder-node-preview' );
            var form = $( '.fl-builder-settings[data-node]' );
            var cssInput = $( '#fl-field-bb_css_code textarea' );

            if ( form.length ) {
                CodeSettings.currentNodeId = form.attr( 'data-node' );
				form.find( '.fl-builder-settings-cancel' ).on( 'click', CodeSettings.clearPreview );
            }
            if ( ! style.length ) {
                $( 'head' ).append( '<style class="fl-builder-node-preview"></style>' );
            }
            if ( cssInput.length ) {
                cssInput.on( 'change', CodeSettings.cssChanged );
            }
        },

        cssChanged: function( e ) {
            var prefix = '.fl-node-' + CodeSettings.currentNodeId;
            var css = CodeSettings.prefixCssSelectors( $( e.target ).val(), prefix );
            $( 'style.fl-builder-node-preview' ).html( css );
        },

        prefixCssSelectors: function( rules, className ) {
            var classLen = className.length, char, nextChar, isAt, isIn;

            className += ' ';
            rules = rules.replace( /\/\*(?:(?!\*\/)[\s\S])*\*\/|[\r\n\t]+/g, '' );
            rules = rules.replace( /}(\s*)@/g, '}@' );
            rules = rules.replace( /}(\s*)}/g, '}}' );

            for ( var i = 0; i < rules.length - 2; i++ ) {
                char = rules[ i ];
                nextChar = rules[ i + 1 ];

                if ( char === '@' ) {
                    isAt = true;
                }
                if ( ! isAt && char === '{' ) {
                    isIn = true;
                }
                if ( isIn && char === '}' ) {
                    isIn = false;
                }
                if (
                    !isIn &&
                    nextChar !== '@' &&
                    nextChar !== '}' &&
                    (
                        char === '}' ||
                        char === ',' ||
                        ( ( char === '{' || char === ';' ) && isAt )
                    )
                ) {
                    rules = rules.slice( 0, i + 1 ) + className + rules.slice( i + 1 );
                    i += classLen;
                    isAt = false;
                }
            };

            if ( rules.indexOf( className ) !== 0 && rules.indexOf( '@' ) !== 0 ) {
                rules = className + rules;
            }

            return rules;
        },
    }

    $( CodeSettings.init );

} )( jQuery );
