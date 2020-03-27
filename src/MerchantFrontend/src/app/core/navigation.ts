/**
 * The NavigationItem base class.
 * Used for the global navigation (sidebar/header)
 */
export class NavigationItem {
  title: string;
  route: string;
  iconName: string;
  children: NavigationItem[];

  // Switches to determine the menu location
  private $sidebar: boolean;
  private $toolbar: boolean;

  constructor(title: string,
              route: string,
              iconName: string = null,
              children: NavigationItem[] = null) {
    this.title = title;
    this.route = route;
    this.iconName = iconName;
    this.children = children;
    this.$sidebar = false;
    this.$toolbar = false;
  }

  public getChildren(): NavigationItem[] | null {
    return this.children;
  }

  public hasChildren(): boolean {
    return this.children != null && this.children.length > 0;
  }

  public appendChild(child: NavigationItem): NavigationItem {
    this.children.push(child);
    return this;
  }

  public setIcon(iconName: string): NavigationItem {
    this.iconName = iconName;
    return this;
  }

  public toolbar(enabled: boolean = true): NavigationItem {
    this.$toolbar = enabled;
    return this;
  }

  public isToolbar(): boolean {
    return this.$toolbar;
  }

  public sidebar(enabled: boolean = true): NavigationItem {
    this.$sidebar = enabled;
    return this;
  }

  public isSideBar(): boolean {
    return this.$sidebar;
  }

}

export const NAVIGATION_ADMIN_MERCHANT = [
  new NavigationItem('Home', '/merchant/profile').setIcon('home').sidebar(),
  new NavigationItem('Informationen', '/merchant/account').setIcon('help-info').sidebar(),
  new NavigationItem('Produkte', '/merchant/products').setIcon('shopping-bag').sidebar(),
  new NavigationItem('Lieferungen', '/merchant/delivery').setIcon('truck').sidebar(),
];

export const NAVIGATION_LANDING = [
  new NavigationItem('Dashboard', ''),
  new NavigationItem('Merchant Area', '/merchant/profile').toolbar(), // TODO: Just for testing
  new NavigationItem('Merchant Login', '/login/merchant').toolbar(),
  new NavigationItem('Merchant Register', '/register/merchant').toolbar(),
  new NavigationItem('Organisation Login', '/login/organization').toolbar(),
  new NavigationItem('Organisation Register', '/register/organization').toolbar(),
];
