import {Component, OnInit} from '@angular/core';
import { NAVIGATION_ADMIN_MERCHANT, NAVIGATION_ADMIN_ORGANIZATION, NavigationItem } from '../../navigation';
import { LoginService } from '../../services/login.service';
import { Router } from '@angular/router';
import { Role, StateService } from '../../state/state.service';

@Component({
  selector: 'portal-admin-page-layout',
  templateUrl: './admin-page-layout.component.html',
  styleUrls: ['./admin-page-layout.component.scss']
})
export class AdminPageLayoutComponent implements OnInit {

  navigation;

  collapsible = true;
  collapsed = true;

  constructor(
    private readonly loginService:LoginService,
    private readonly router: Router,
    private readonly stateService: StateService
  ) {}

  ngOnInit(): void {
    this.stateService.getLoggedInRole().subscribe(
      (role: Role) => {
        if (role === Role.organization) {
          this.navigation = NAVIGATION_ADMIN_ORGANIZATION;
        } else if (role === Role.merchant) {
          this.navigation = NAVIGATION_ADMIN_MERCHANT;
        }
      }
    )
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
