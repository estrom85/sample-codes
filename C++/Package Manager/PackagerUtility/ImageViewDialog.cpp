/*
 * File:   ImageViewDialog.cpp
 * Author: mato
 *
 * Created on Streda, 2013, december 11, 19:59
 */

#include "ImageViewDialog.h"

ImageViewDialog::ImageViewDialog(string path) {
    widget.setupUi(this);
    connect(this->widget.pushButton, SIGNAL(clicked()), this, SLOT(CloseDialog()));
    image = new QPixmap(path.c_str());
    w_diff=width() - widget.imgView->width();
    h_diff=height() - widget.imgView->height();
    this->setWindowTitle(QString("Image Preview"));
    setImage();
}

ImageViewDialog::~ImageViewDialog() {
    delete image;
}

void ImageViewDialog::CloseDialog() {
    this->close();
}

void ImageViewDialog::resizeEvent(QResizeEvent* event){
    
    widget.imgView->setFixedSize(
        width()-w_diff,height()-h_diff);
    setImage();
    widget.pushButton->setGeometry(
        width()-widget.pushButton->width()-10,height()-widget.pushButton->height()-10,
            widget.pushButton->width(),widget.pushButton->height());
}

void ImageViewDialog::setImage(){
    widget.imgView->setPixmap(
            image->scaled(
            widget.imgView->width(),
            widget.imgView->height(),
            Qt::KeepAspectRatio,
            Qt::SmoothTransformation));
}