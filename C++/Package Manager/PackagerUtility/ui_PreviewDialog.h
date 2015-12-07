/********************************************************************************
** Form generated from reading UI file 'PreviewDialog.ui'
**
** Created: Fri Dec 13 10:44:25 2013
**      by: Qt User Interface Compiler version 4.8.1
**
** WARNING! All changes made in this file will be lost when recompiling UI file!
********************************************************************************/

#ifndef UI_PREVIEWDIALOG_H
#define UI_PREVIEWDIALOG_H

#include <QtCore/QVariant>
#include <QtGui/QAction>
#include <QtGui/QApplication>
#include <QtGui/QButtonGroup>
#include <QtGui/QDialog>
#include <QtGui/QGridLayout>
#include <QtGui/QHBoxLayout>
#include <QtGui/QHeaderView>
#include <QtGui/QLabel>
#include <QtGui/QPushButton>
#include <QtGui/QSpacerItem>
#include <QtGui/QStackedWidget>
#include <QtGui/QWidget>
#include "AudioSeekBar.h"

QT_BEGIN_NAMESPACE

class Ui_PreviewDialog
{
public:
    QGridLayout *gridLayout;
    QPushButton *prevBtn;
    QPushButton *closeBtn;
    QStackedWidget *prevWidget;
    QWidget *imagePage;
    QHBoxLayout *horizontalLayout;
    QLabel *imgPrev;
    QWidget *AudioPage;
    QGridLayout *gridLayout_2;
    QSpacerItem *verticalSpacer;
    QSpacerItem *verticalSpacer_3;
    QPushButton *stopBttn;
    QPushButton *pauseBttn;
    QPushButton *playBttn;
    AudioSeekBar *progressBar;
    QLabel *progDisplay;
    QWidget *FontPage;
    QGridLayout *gridLayout_3;
    QLabel *label;
    QWidget *UnknownPage;
    QGridLayout *gridLayout_4;
    QLabel *label_2;
    QPushButton *nextBtn;

    void setupUi(QDialog *PreviewDialog)
    {
        if (PreviewDialog->objectName().isEmpty())
            PreviewDialog->setObjectName(QString::fromUtf8("PreviewDialog"));
        PreviewDialog->resize(571, 439);
        QSizePolicy sizePolicy(QSizePolicy::Fixed, QSizePolicy::Fixed);
        sizePolicy.setHorizontalStretch(0);
        sizePolicy.setVerticalStretch(0);
        sizePolicy.setHeightForWidth(PreviewDialog->sizePolicy().hasHeightForWidth());
        PreviewDialog->setSizePolicy(sizePolicy);
        PreviewDialog->setMinimumSize(QSize(571, 439));
        PreviewDialog->setMaximumSize(QSize(571, 439));
        gridLayout = new QGridLayout(PreviewDialog);
        gridLayout->setObjectName(QString::fromUtf8("gridLayout"));
        gridLayout->setSizeConstraint(QLayout::SetDefaultConstraint);
        prevBtn = new QPushButton(PreviewDialog);
        prevBtn->setObjectName(QString::fromUtf8("prevBtn"));

        gridLayout->addWidget(prevBtn, 1, 0, 1, 1);

        closeBtn = new QPushButton(PreviewDialog);
        closeBtn->setObjectName(QString::fromUtf8("closeBtn"));

        gridLayout->addWidget(closeBtn, 2, 1, 1, 1);

        prevWidget = new QStackedWidget(PreviewDialog);
        prevWidget->setObjectName(QString::fromUtf8("prevWidget"));
        imagePage = new QWidget();
        imagePage->setObjectName(QString::fromUtf8("imagePage"));
        horizontalLayout = new QHBoxLayout(imagePage);
        horizontalLayout->setObjectName(QString::fromUtf8("horizontalLayout"));
        imgPrev = new QLabel(imagePage);
        imgPrev->setObjectName(QString::fromUtf8("imgPrev"));

        horizontalLayout->addWidget(imgPrev);

        prevWidget->addWidget(imagePage);
        AudioPage = new QWidget();
        AudioPage->setObjectName(QString::fromUtf8("AudioPage"));
        gridLayout_2 = new QGridLayout(AudioPage);
        gridLayout_2->setObjectName(QString::fromUtf8("gridLayout_2"));
        verticalSpacer = new QSpacerItem(20, 40, QSizePolicy::Minimum, QSizePolicy::Expanding);

        gridLayout_2->addItem(verticalSpacer, 0, 0, 1, 1);

        verticalSpacer_3 = new QSpacerItem(20, 40, QSizePolicy::Minimum, QSizePolicy::Expanding);

        gridLayout_2->addItem(verticalSpacer_3, 4, 1, 1, 1);

        stopBttn = new QPushButton(AudioPage);
        stopBttn->setObjectName(QString::fromUtf8("stopBttn"));

        gridLayout_2->addWidget(stopBttn, 3, 1, 1, 1);

        pauseBttn = new QPushButton(AudioPage);
        pauseBttn->setObjectName(QString::fromUtf8("pauseBttn"));

        gridLayout_2->addWidget(pauseBttn, 3, 2, 1, 1);

        playBttn = new QPushButton(AudioPage);
        playBttn->setObjectName(QString::fromUtf8("playBttn"));

        gridLayout_2->addWidget(playBttn, 3, 3, 1, 1);

        progressBar = new AudioSeekBar(AudioPage);
        progressBar->setObjectName(QString::fromUtf8("progressBar"));
        progressBar->setMouseTracking(true);
        progressBar->setMaximum(10000);
        progressBar->setOrientation(Qt::Horizontal);
        progressBar->setTickPosition(QSlider::NoTicks);
        progressBar->setTickInterval(500);

        gridLayout_2->addWidget(progressBar, 1, 0, 1, 4);

        progDisplay = new QLabel(AudioPage);
        progDisplay->setObjectName(QString::fromUtf8("progDisplay"));

        gridLayout_2->addWidget(progDisplay, 3, 0, 1, 1);

        prevWidget->addWidget(AudioPage);
        FontPage = new QWidget();
        FontPage->setObjectName(QString::fromUtf8("FontPage"));
        gridLayout_3 = new QGridLayout(FontPage);
        gridLayout_3->setObjectName(QString::fromUtf8("gridLayout_3"));
        label = new QLabel(FontPage);
        label->setObjectName(QString::fromUtf8("label"));
        label->setWordWrap(true);

        gridLayout_3->addWidget(label, 0, 0, 1, 1);

        prevWidget->addWidget(FontPage);
        UnknownPage = new QWidget();
        UnknownPage->setObjectName(QString::fromUtf8("UnknownPage"));
        gridLayout_4 = new QGridLayout(UnknownPage);
        gridLayout_4->setObjectName(QString::fromUtf8("gridLayout_4"));
        label_2 = new QLabel(UnknownPage);
        label_2->setObjectName(QString::fromUtf8("label_2"));
        QFont font;
        font.setFamily(QString::fromUtf8("Times New Roman"));
        font.setPointSize(20);
        font.setBold(true);
        font.setItalic(true);
        font.setWeight(75);
        label_2->setFont(font);
        label_2->setAlignment(Qt::AlignCenter);

        gridLayout_4->addWidget(label_2, 0, 0, 1, 1);

        prevWidget->addWidget(UnknownPage);

        gridLayout->addWidget(prevWidget, 0, 0, 1, 2);

        nextBtn = new QPushButton(PreviewDialog);
        nextBtn->setObjectName(QString::fromUtf8("nextBtn"));

        gridLayout->addWidget(nextBtn, 1, 1, 1, 1);


        retranslateUi(PreviewDialog);

        prevWidget->setCurrentIndex(1);


        QMetaObject::connectSlotsByName(PreviewDialog);
    } // setupUi

    void retranslateUi(QDialog *PreviewDialog)
    {
        PreviewDialog->setWindowTitle(QApplication::translate("PreviewDialog", "PreviewDialog", 0, QApplication::UnicodeUTF8));
        prevBtn->setText(QApplication::translate("PreviewDialog", "<<", 0, QApplication::UnicodeUTF8));
        closeBtn->setText(QApplication::translate("PreviewDialog", "Close", 0, QApplication::UnicodeUTF8));
        imgPrev->setText(QString());
        stopBttn->setText(QApplication::translate("PreviewDialog", "Stop", 0, QApplication::UnicodeUTF8));
        pauseBttn->setText(QApplication::translate("PreviewDialog", "Pause", 0, QApplication::UnicodeUTF8));
        playBttn->setText(QApplication::translate("PreviewDialog", "Play", 0, QApplication::UnicodeUTF8));
        progDisplay->setText(QString());
        label->setText(QApplication::translate("PreviewDialog", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer malesuada laoreet leo. Quisque in arcu a quam porta tincidunt in at lacus. Sed sed ipsum eu sapien laoreet ullamcorper. Aenean vel lacinia arcu. Mauris quis lectus vitae purus aliquam volutpat. Sed bibendum nibh vel dictum tempor. In hac habitasse platea dictumst. Integer vitae viverra quam. Proin eu diam condimentum, sollicitudin nisl ut, porttitor dolor. Fusce tortor est, eleifend sed tincidunt ac, pulvinar quis est. Morbi adipiscing facilisis justo, eget interdum odio volutpat in. Nunc enim mauris, molestie at mauris et, cursus condimentum tortor. Quisque viverra ultrices turpis eget cursus. Aenean sed vehicula massa, ac euismod augue.", 0, QApplication::UnicodeUTF8));
        label_2->setText(QApplication::translate("PreviewDialog", "No preview available", 0, QApplication::UnicodeUTF8));
        nextBtn->setText(QApplication::translate("PreviewDialog", ">>", 0, QApplication::UnicodeUTF8));
    } // retranslateUi

};

namespace Ui {
    class PreviewDialog: public Ui_PreviewDialog {};
} // namespace Ui

QT_END_NAMESPACE

#endif // UI_PREVIEWDIALOG_H
