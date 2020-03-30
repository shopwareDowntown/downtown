import { Component, OnInit } from '@angular/core';
import {NAVIGATION_LANDING} from '../../navigation';
import { timeout } from 'rxjs/operators';

@Component({
  selector: 'portal-landing-page-layout',
  templateUrl: './landing-page-layout.component.html',
  styleUrls: ['./landing-page-layout.component.scss']
})
export class LandingPageLayoutComponent implements OnInit {

  readonly navigation = NAVIGATION_LANDING;

  showLogin = false;

  constructor() { }

  ngOnInit(): void {
  }

  openLogin(): void {
    this.showLogin = false;
    setTimeout(() => {
      this.showLogin = true;
    });
  }
}
