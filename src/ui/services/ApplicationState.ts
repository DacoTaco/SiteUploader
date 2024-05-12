import { Injectable } from '@angular/core';
import { UserInformation } from '../models'

@Injectable({
  providedIn: 'root'
})

export class ApplicationState
{
  public User: UserInformation | null;

  constructor()
  {
    this.User = null;
  }

  public isAuthenticated(): boolean
  {
    if(this.User == null || this.User.expirationDate < new Date())
      return false;

    return true;
  }
}