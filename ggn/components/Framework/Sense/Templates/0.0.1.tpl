<!doctype HTML>

<html lang="{{Settings:Lang}}">

    <head sense-helmet="default">

        <style type="text/css">

            sense-chassis[Chassis\:Pending],sense-splashscreen{display:none;}

            sense-splashscreen > * {
                opacity:0;
                transition: all 360ms ease-in-out;
            }

            :root,html,body{height:100vh;}

            [sense-splash="Label"]{

                font-size:1.5em;

            }

            [sense-splash="Icon"]{

                font-size:2.1em;

                padding:16px;

            }

            [sense-splash="Payload:Progress"]{

                padding:12px 0;

                display:flex;

                display:-webkit-flex;

                align-items:center;

                justify-content:center;

                flex-direction:column;

            }

            [sense-payload="Progress:Bar"]{

                width:120px;

                height:.38rem;

                background-color:rgba(0,0,0,.2);

                border-radius:20px;

            }

            [sense-payload="Progress:Track"]{

                width:0%;

                height:100%;

                background-color:#fff;

                border-radius:inherit;

                transition:all 200ms ease;

            }

            [sense-payload="Progress:Info"]{

                display:flex;

                width:128px;

                padding:8px 0;

            }

            [sense-payload="Progress:Loaded"]{

                text-align:right;

            }

            [sense-payload="Progress:Total"]{

                text-align:right;

            }

            [sense-payload="Progress:Percent"]{

                text-align:left;

                flex:1 auto;

            }

            body[sense-chassis="default"]{
/*
                position:relative;
*/
                overflow-x:hidden;

                overflow-y:hidden;

            }

      </style>

        <title>{{App:Title}}</title>

        <script type="text/javascript">{{Sense:Engine:Settings}}</script>

        <script src="{{Http:Host}}assets/js/ggn/core/Senju-0.0.1.js"></script>

        <script type="module" src="{{Http:Host}}assets/js/ggn/mods/Ui.Kit.Controller-0.0.1.js"></script>
    
        <script type="module" src="{{Http:Host}}assets/js/sense/core/Mokuton-0.0.1.js"></script>

        <script type="module" src="{{Http:Host}}assets/js/sense/mods/Core.Assembly.js"></script>
    
        {{Sense:Engine:Head}}

    </head>

    <body sense-chassis="default">

        {{Sense:Engine:Body}}

    </body>

</html>