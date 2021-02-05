<?php

namespace Framework\Mokian;

    use Framework\Application;


Class Menu{

    static public function Build(Object $Menu){

?>

let $Menu = <?=json_encode($Menu);?>;

if(typeof $Menu == 'object'){

    Object.keys($Menu).forEach(($Key)=>{

        let menu = $Menu[$Key];

        if(typeof menu == 'object'){

            var m = new Mokian.Menu($Key,{

                ShowLabel: menu.ShowLabel||true

                , ShowGlyph: menu.ShowGlyph||false

                , Responsive: menu.Responsive||true

                , ForceResponsive: menu.ForceResponsive||false

                , Title: menu.Title||false
                
            });

            Object.values(menu.Items||[]).forEach((item)=>{

                m.AddItem({

                    Glyph: Mokian.Ui.SetGlyph(item.Glyph)

                    , Label: item.Label||''

                    , Link: (item.Link||'')

                    , Action: function(ev){

                        eval(item.Action||"");

                    }

                });

            });
            
        }

    });
    
}

<?php
        
    }

}