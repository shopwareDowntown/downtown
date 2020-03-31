import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'portal-landing-page-layout',
  templateUrl: './landing-page-layout.component.html',
  styleUrls: ['./landing-page-layout.component.scss']
})
export class LandingPageLayoutComponent implements OnInit {

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
