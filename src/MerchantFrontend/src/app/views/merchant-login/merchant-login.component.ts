import { Component, OnInit } from '@angular/core';
import {Router} from '@angular/router';
import { LoginService } from '../../core/services/login.service';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Observable } from 'rxjs';
import { Authority } from '../../core/models/authority.model';

@Component({
  selector: 'portal-merchant-login',
  templateUrl: './merchant-login.component.html',
  styleUrls: ['./merchant-login.component.scss']
})
export class MerchantLoginComponent implements OnInit {

  username: string;
  password: string;
  selectedAuthority: Authority;
  isLogging: boolean;
  loginFailed = false;

  constructor(
    private router: Router,
    private loginService: LoginService
  ) { }

  ngOnInit(): void {
  }

  enterLogin($event: KeyboardEvent) {
    // listen for enter key to login
    if ($event.code === 'Enter') {
      this.doLogin();
    }
  }

  public doLogin() {
    this.loginFailed = false;
    this.loginService.login(this.username, this.password)
      .subscribe((result) => {
        this.router.navigate(['/merchant/profile']);

      },() => {
        this.loginFailed = true;
      });
  }
}
