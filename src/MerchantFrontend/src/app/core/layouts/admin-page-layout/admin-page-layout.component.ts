import {Component, OnInit} from '@angular/core';
import {NAVIGATION_ADMIN, NavigationItem} from '../../navigation';

@Component({
  selector: 'portal-admin-page-layout',
  templateUrl: './admin-page-layout.component.html',
  styleUrls: ['./admin-page-layout.component.scss']
})
export class AdminPageLayoutComponent implements OnInit {

  readonly navigation = NAVIGATION_ADMIN;

  collapsible = true;
  collapsed = true;

  constructor() {
  }

  ngOnInit(): void {
  }

  public get toolbarNav(): NavigationItem[]{
    return this.navigation.filter(value => value.isToolbar());
  }

  public get sidebarNav(): NavigationItem[]{
    return this.navigation.filter(value => value.isSideBar());
  }

}
