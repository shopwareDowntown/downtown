import { Component, OnInit } from '@angular/core';
import {Router} from '@angular/router';

@Component({
  selector: 'portal-merchant-login',
  templateUrl: './merchant-login.component.html',
  styleUrls: ['./merchant-login.component.scss']
})
export class MerchantLoginComponent implements OnInit {

  username: string;
  password: string;
  isLogging: boolean;

  constructor(private router: Router) { }

  ngOnInit(): void {
  }

  enterLogin($event: KeyboardEvent) {
    // listen for enter key to login
    if ($event.code === 'Enter') {
      this.doLogin();
    }
  }

  public doLogin() {
    // TODO: Authentication
    this.router.navigate(['/']);
  }
}
