import { TestBed } from '@angular/core/testing';
import { UploadComponent } from './upload.component';

describe('UploadComponent', () => {
  beforeEach(async () => {
    TestBed.configureTestingModule({
      declarations: [
        UploadComponent
      ],
    }).compileComponents();
  });

  it('should create the app', () => {
    const fixture = TestBed.createComponent(UploadComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  });

  it(`should have as title 'dnd'`, () => {
    const fixture = TestBed.createComponent(UploadComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app.title).toEqual('dnd');
  });

  it('should render title', () => {
    const fixture = TestBed.createComponent(UploadComponent);
    fixture.detectChanges();
    const compiled = fixture.debugElement.nativeElement;
    expect(compiled.querySelector('.content span').textContent).toContain('dnd app is running!');
  });
});