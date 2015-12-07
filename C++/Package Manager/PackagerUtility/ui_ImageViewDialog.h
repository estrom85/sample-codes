/********************************************************************************
** Form generated from reading UI file 'ImageViewDialog.ui'
**
** Created: Wed Dec 11 20:00:42 2013
**      by: Qt User Interface Compiler version 4.8.1
**
** WARNING! All changes made in this file will be lost when recompiling UI file!
********************************************************************************/

#ifndef UI_IMAGEVIEWDIALOG_H
#define UI_IMAGEVIEWDIALOG_H

#include <QtCore/QVariant>
#include <QtGui/QAction>
#include <QtGui/QApplication>
#include <QtGui/QButtonGroup>
#include <QtGui/QDialog>
#include <QtGui/QHeaderView>
#include <QtGui/QLabel>
#include <QtGui/QPushButton>

QT_BEGIN_NAMESPACE

class Ui_ImageViewDialog
{
public:
    QLabel *imgView;
    QPushButton *pushButton;

    void setupUi(QDialog *ImageViewDialog)
    {
        if (ImageViewDialog->objectName().isEmpty())
            ImageViewDialog->setObjectName(QString::fromUtf8("ImageViewDialog"));
        ImageViewDialog->resize(400, 300);
        imgView = new QLabel(ImageViewDialog);
        imgView->setObjectName(QString::fromUtf8("imgView"));
        imgView->setGeometry(QRect(10, 10, 381, 251));
        pushButton = new QPushButton(ImageViewDialog);
        pushButton->setObjectName(QString::fromUtf8("pushButton"));
        pushButton->setGeometry(QRect(300, 270, 94, 24));

        retranslateUi(ImageViewDialog);

        QMetaObject::connectSlotsByName(ImageViewDialog);
    } // setupUi

    void retranslateUi(QDialog *ImageViewDialog)
    {
        ImageViewDialog->setWindowTitle(QApplication::translate("ImageViewDialog", "ImageViewDialog", 0, QApplication::UnicodeUTF8));
        imgView->setText(QString());
        pushButton->setText(QApplication::translate("ImageViewDialog", "Close", 0, QApplication::UnicodeUTF8));
    } // retranslateUi

};

namespace Ui {
    class ImageViewDialog: public Ui_ImageViewDialog {};
} // namespace Ui

QT_END_NAMESPACE

#endif // UI_IMAGEVIEWDIALOG_H
