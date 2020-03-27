import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class LocalStorageService {

  setItem(key: string, value: string): void {
    if (!this.isSupported()) {
      return;
    }
    localStorage.setItem(key, value);
  }

  getItem(key: string): string|null {
    if (!this.isSupported()) {
      return null;
    }
    return localStorage.getItem(key);
  }

  removeItem(key: string): void {
    if (!this.isSupported()) {
      return null;
    }
    return localStorage.removeItem(key);
  }

  private isSupported(): boolean {
    const test = 'test';
    try {
      localStorage.setItem(test, test);
      localStorage.removeItem(test);
      return true;
    } catch(e) {
      return false;
    }
  }
}
