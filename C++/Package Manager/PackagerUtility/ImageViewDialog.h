/* 
 * File:   ImageViewDialog.h
 * Author: mato
 *
 * Created on Streda, 2013, december 11, 19:59
 */

#ifndef _IMAGEVIEWDIALOG_H
#define	_IMAGEVIEWDIALOG_H

#include "ui_ImageViewDialog.h"
#include <string>

using namespace std;

class ImageViewDialog : public QDialog {
    Q_OBJECT
public:
    ImageViewDialog(string path);
    virtual ~ImageViewDialog();
private:
    Ui::ImageViewDialog widget;
    QPixmap* image;
    int w_diff;
    int h_diff;
protected:
    virtual void resizeEvent(QResizeEvent*);
public slots:
    void CloseDialog();
    
private:
    void setImage();
};

#endif	/* _IMAGEVIEWDIALOG_H */
