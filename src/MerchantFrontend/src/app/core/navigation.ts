/**
 * The NavigationItem base class.
 * Used for the global navigation (sidebar/header)
 */
import { setIconAlias } from '@clr/core/icon-shapes/utils/icon.service-helpers';

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
  new NavigationItem('SIDEBAR.HOME', '/merchant/home').setIcon('home').sidebar(),
  new NavigationItem('SIDEBAR.INFORMATION', '/merchant/profile').setIcon('help-info').sidebar(),
  new NavigationItem('SIDEBAR.PRODUCTS', '/merchant/products').setIcon('shopping-bag').sidebar(),
  new NavigationItem('COMMON.ORDERS', '/merchant/orders').setIcon('bundle').sidebar(),
  new NavigationItem('SIDEBAR.VOUCHERS', '/merchant/vouchers').setIcon('shopping-bag').sidebar(),
  //new NavigationItem('Lieferungen', '/merchant/delivery').setIcon('truck').sidebar(),
];

export const NAVIGATION_ADMIN_ORGANIZATION = [
  new NavigationItem('SIDEBAR.HOME', '/organization/home').setIcon('home').sidebar(),
  new NavigationItem('SIDEBAR.INFORMATION', '/organization/profile').setIcon('help-info').sidebar(),
  new NavigationItem('SIDEBAR.MERCHANTS', '/organization/merchants').setIcon('store').sidebar(),
  new NavigationItem('SIDEBAR.DISCLAIMER', '/organization/disclaimer').setIcon('bullseye').sidebar()
];
