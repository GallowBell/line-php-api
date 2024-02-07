<?php 

/**
 * REQUEST_URI
 * @var string $req_URL
 */
$req_URL = explode('?', $_SERVER['REQUEST_URI'])[0];

$current_page = $APP_URL . $req_URL;

$ADMIN_URL = $APP_URL . '/admin';

$access_level = $UserData[0]['access_level'];

//for more icon
//https://demos.themeselection.com/materio-bootstrap-html-admin-template/html/vertical-menu-template/icons-mdi.html

/**
 * Menu List
 * @var array $menu_lists
 * * name => string
 * * icon => string
 * * link => string
 * * sub_menu => array
 * * * sub_menu['name'] => string
 * * * sub_menu['icon'] => string
 * * * sub_menu['link'] => string
 * * * sub_menu['target'] => string
 */
$menu_lists = [
    [
        'name' => 'Dashboard',
        'icon' => 'mdi-home-outline',
        'link' => $APP_URL . '/admin/',
        'access_level' => 10,
        'sub_menu' => []
    ],
    [
      'name' => 'LINE Bot Manage',
      'icon' => 'mdi-robot-outline',
      'link' => $APP_URL . '/admin/line-bot-manage',
      'access_level' => 10,
      'sub_menu' => []
    ],
    [
        'name' => 'User Manage',
        'icon' => 'mdi-account-outline',
        'link' => 'javascript:void(0);',
        'access_level' => 100,
        'sub_menu' => [
            [
                'name' => 'List User',
                'icon' => 'mdi-home-outline',
                'link' => $APP_URL . '/admin/user-manage',
                'access_level' => 100,
                'target' => ''
            ]
        ]
    ],
];


$html_left_menu = '';
foreach ($menu_lists as $key => $value) {
    save_log('menu_lists => ' . json_encode($value));
    if($access_level < $value['access_level']) {
      continue;
    }
    $count_sub_menu = count($value['sub_menu']);
    $id = 'menu-item-id-' . $key;
    $html_left_menu .= '<li id="'.$id.'" class="menu-item  ' . ($current_page == $value['link'] ? ' active ' : '') . ' ">';
        $html_left_menu .= '<a href="' . $value['link'] . '" class="menu-link waves-effect '.($count_sub_menu > 0 ? ' menu-toggle ': '').' ">';
            $html_left_menu .= '<i class="menu-icon tf-icons mdi ' . $value['icon'] . ' "></i>';
            $html_left_menu .= '<div data-i18n="Dashboards">' . $value['name'] . '</div>';
        $html_left_menu .= '</a>';        
        foreach ($value['sub_menu'] as $key_sub => $value_sub) {

            if($access_level < $value_sub['access_level']) {
              continue;
            }

            if($key_sub == 0){
                $html_left_menu .= '<ul class="menu-sub">';
            }

            $html_left_menu .= '<li class="menu-item ' . ($current_page == $value_sub['link'] ? 'active' : '') . '">
                <a
                    href="'.$value_sub['link'].'"
                    target="'.$value_sub['target'].'"
                    class="menu-link waves-effect">
                    <div data-i18n="' . $value_sub['name'].'">'.$value_sub['name'] . '</div>
                </a>
            </li>';

            if($current_page == $value_sub['link']) {
                $script = '<script>let custom_btnLink = document.getElementById("'.$id.'")?document.getElementById("'.$id.'"):``;if(custom_btnLink){custom_btnLink.classList.add("active");custom_btnLink.classList.add("open");}</script>';
                $html_left_menu .= $script;
            }

            if(($key_sub+1) == $count_sub_menu){
                $html_left_menu .= '</ul>';
            }
        }
    
    $html_left_menu .= '</li>';

}

?>        
        <!-- / Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="<?php echo $ADMIN_URL; ?>" class="app-brand-link">
              <span class="app-brand-logo demo me-1">
                <span style="color: var(--bs-primary)">
                
                  <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z"
                      fill="currentColor" />
                    <path
                      opacity="0.077704"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z"
                      fill="black" />
                    <path
                      opacity="0.077704"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M0 65.2656L60.4839 99.0795V119.859L0 65.2656Z"
                      fill="black" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z"
                      fill="currentColor" />
                    <path
                      opacity="0.077704"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M250 65.2656L189.516 99.8897V135.006L250 65.2656Z"
                      fill="black" />
                    <path
                      opacity="0.077704"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M250 65.2656L189.516 99.0497V120.886L250 65.2656Z"
                      fill="black" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                      fill="currentColor" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                      fill="white"
                      fill-opacity="0.15" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                      fill="currentColor" />
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                      fill="white"
                      fill-opacity="0.3" />
                  </svg>

                </span>
              </span>
              <span class="app-brand-text demo menu-text fw-semibold ms-2">Materio</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <?php 
                //menu list html
                echo $html_left_menu;
            ?>
          </ul>
        </aside>
        <!-- / Menu -->