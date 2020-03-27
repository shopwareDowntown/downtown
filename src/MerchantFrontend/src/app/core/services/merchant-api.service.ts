import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Merchant, MerchantRegistration, MerchantLoginResult } from '../models/merchant.model';
import { Product } from '../models/product.model';
import { Authority } from '../models/authority.model';
import { StateService } from '../state/state.service';
import { map, take } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class MerchantApiService {

  private merchantSwAccessKey = 'SWSCJTWTSBF_G77_ML-5KRPWTA';
  private apiUrl = 'https://sw6.ovh';

  constructor(
    private readonly http: HttpClient,
    private stateService: StateService
  ) { }

  login(username: string, password: string, authorityAccessKey: string): Observable<MerchantLoginResult> {

    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('sw-access-key', authorityAccessKey);

    const body = JSON.stringify(
      {
        username: username,
        password: password,
      }
    );
    return this.http.post<MerchantLoginResult>(this.apiUrl + '/sales-channel-api/v1/customer/login', body, {headers: headers});
  }

  // merchant routes

  registerMerchant(merchantRegistration: MerchantRegistration, accessKey: string): Observable<any> {
    let headers = new HttpHeaders();
    headers = headers.set('content-type', 'application/json');
    headers = headers.set('sw-access-key', accessKey);
    return this.http.post<any>(this.apiUrl + '/merchant-api/v1/register', JSON.stringify(merchantRegistration), {headers: headers});
  }

  getMerchant(): Observable<Merchant> {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('sw-access-key', this.getAuthorityAccessKey());
    headers = headers.set('sw-context-token', this.getSwContextToken());

    return this.http.get<any>(this.apiUrl + '/sales-channel-api/v1/customer', {headers: headers})
      .pipe(
        map((result: any) => {
          let data = result.data;
          return {
            id: data.id as string,
            email: data.email as string,
            password: '',
            name: data.company || '',
            firstName: data.firstName,
            lastName: data.lastName,
            salutation: data.salutation.salutationKey || '',
            street: data.defaultBillingAddress.street,
            zipCode: data.defaultBillingAddress.zipcode,
            city: data.defaultBillingAddress.city,
            country: data.defaultBillingAddress.countryId,
            phoneNumber: data.defaultBillingAddress.phoneNumber as string
          };
        })
      );
  }

  updateMerchant(merchant: Merchant): Observable<Merchant> {
      return this.http.patch<Merchant>(this.apiUrl + '/merchant-api/profile', JSON.stringify(merchant), {headers: this.getHeaders() });
  }

  // product routes

  getProducts(): Observable<{ data: Product[]}> {
      return this.http.get<{ data: Product[]}>(this.apiUrl + '/merchant-api/v1/products', {headers: this.getHeaders() });
  }

  getProduct(productId: number): Observable<Product> {
      return this.http.get<Product>(this.apiUrl + '/merchant-api/v1/products/' + productId, {headers: this.getJsonContentTypeHeaders() });
  }

  addProduct(product: Product): Observable<Product> {
      return this.http.post<Product>(this.apiUrl + '/merchant-api/v1/products', JSON.stringify(product), {headers: this.getJsonContentTypeHeaders() });
  }

  updateProduct(product: Product): Observable<Product> {
    return this.http.put<Product>(this.apiUrl + '/merchant-api/v1/products/'  + product.id, JSON.stringify(product), {headers: this.getJsonContentTypeHeaders() });
  }

  deleteProduct(product: Product): Observable<void> {
      return this.http.delete<void>(this.apiUrl + '/merchant-api/products/' + product.id, {headers: this.getJsonContentTypeHeaders() });
  }

  // authority route

  getAuthorities(): Observable<Authority[]> {
      return this.http.get<Authority[]>(this.apiUrl + '/merchant-api/v1/authorities');
  }

  private getHeaders(): { [header: string]: string | string[];} {
    return {
      'sw-access-key': this.getSwAccessKey(),
      'sw-context-token': this.getSwContextToken()
    };
  }

  private getJsonContentTypeHeaders(): HttpHeaders {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('sw-access-key', this.getAuthorityAccessKey());
    headers = headers.set('sw-context-token', this.getSwContextToken());
    return headers;
  }


  private getSwAccessKey(): string {
    let key: string;
    this.stateService.getAuthority()
      .pipe(
        take(1)
      )
      .subscribe(authority => key = authority.accessKey);

    return key ? key : '';
  }

  private getAuthorityAccessKey(): string {
    let accessKey: string;
    this.stateService.getAuthority().pipe(
      take(1)
    )
      .subscribe((authority: Authority) => {
        accessKey = authority.accessKey
      });

    return accessKey ? accessKey : '';
  }

  private getSwContextToken(): string {
    let token: string;
    this.stateService.getSwContextToken()
      .pipe(
        take(1)
      )
      .subscribe(t => token = t);

    return token ? token : '';
  }
}
