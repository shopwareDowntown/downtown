/**
 * The NavigationItem base class.
 * Used for the global navigation (sidebar/header)
 */
export class NavigationItem {
  title: string;
  route: string;
  iconName: string;
  children: NavigationItem[];


  constructor(title: string,
              route: string,
              iconName: string = null,
              children: NavigationItem[] = null) {
    this.title = title;
    this.route = route;
    this.iconName = iconName;
    this.children = children;
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

}

export const NAVIGATION_ADMIN = [
  new NavigationItem('Dashboard', '').setIcon('unknown-status'),
  new NavigationItem('Merchant Profile', '/merchant/profile').setIcon('unknown-status'),
  new NavigationItem('Merchant Settings', '/merchant/settings').setIcon('unknown-status'),
];

export const NAVIGATION_LANDING = [
  new NavigationItem('Dashboard', ''),
  new NavigationItem('Merchant Area', '/merchant/profile'), // TODO: Just for testing
  new NavigationItem('Merchant Login', '/login/merchant'),
  new NavigationItem('Merchant Register', '/register/merchant'),
  new NavigationItem('Organisation Login', '/login/organization'),
  new NavigationItem('Organisation Register', '/register/organization'),
];
