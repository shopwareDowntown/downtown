import {Component, OnInit} from '@angular/core';
import {NAVIGATION_ADMIN_MERCHANT, NavigationItem} from '../../navigation';
import { LoginService } from '../../services/login.service';
import { Router } from '@angular/router';

@Component({
  selector: 'portal-admin-page-layout',
  templateUrl: './admin-page-layout.component.html',
  styleUrls: ['./admin-page-layout.component.scss']
})
export class AdminPageLayoutComponent implements OnInit {

  readonly navigation = NAVIGATION_ADMIN_MERCHANT;

  collapsible = true;
  collapsed = true;

  constructor(
    private readonly loginService:LoginService,
    private readonly router: Router
  ) {}

  ngOnInit(): void {
  }

  public get toolbarNav(): NavigationItem[]{
    return this.navigation.filter(value => value.isToolbar());
  }

  public get sidebarNav(): NavigationItem[]{
    return this.navigation.filter(value => value.isSideBar());
  }

  doLogout(): void {
    this.loginService.logout();
  }

}
