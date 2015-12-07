/*
 * File:   PreviewDialog.cpp
 * Author: mato
 *
 * Created on Štvrtok, 2013, december 12, 9:05
 */

#include "PreviewDialog.h"
#include <iostream>
#include <qt4/QtGui/qfontdatabase.h>

PreviewDialog::PreviewDialog(QWidget* parent) : QDialog(parent) {
    widget.setupUi(this);
    package = 0;
    img = 0;
    this->fontId = -1;
    /*init connections*/
    connect(widget.prevBtn, SIGNAL(clicked()), this, SLOT(Prev()));
    connect(widget.nextBtn, SIGNAL(clicked()), this, SLOT(Next()));
    connect(widget.closeBtn, SIGNAL(clicked()), this, SLOT(CloseDialog()));

    audio = new Phonon::MediaObject(this);
    Phonon::createPath(audio,
            new Phonon::AudioOutput(Phonon::MusicCategory, this));

    audio->setTickInterval(500);
    widget.progressBar->setTickInterval(500);

    connect(widget.playBttn, SIGNAL(clicked()), audio, SLOT(play()));
    connect(widget.pauseBttn, SIGNAL(clicked()), audio, SLOT(pause()));
    connect(widget.stopBttn, SIGNAL(clicked()), audio, SLOT(stop()));
    //connect(audio,SIGNAL(tick()),widget.horizontalSlider,SLOT());
    connect(widget.progressBar, SIGNAL(sliderMoved(int)), this, SLOT(SetAudioPosition(int)));
    connect(audio, SIGNAL(tick(qint64)), this, SLOT(SetTicks(qint64)));
    connect(audio, SIGNAL(stateChanged(Phonon::State, Phonon::State)),
            this, SLOT(stateChanged(Phonon::State)));
    setFixedSize(517, 418);

    setView();
}

PreviewDialog::~PreviewDialog() {
    delete img;
}

void PreviewDialog::ChangePackage(Package* package) {
    this->package = package;
    selectedIndex = 0;
}

void PreviewDialog::ShowDialog() {
    setView();
    this->show();

}

void PreviewDialog::ShowDialog(const QModelIndex index) {
    selectedIndex = index.row();
    setView();
    this->show();
}

void PreviewDialog::Next() {
    if (package == 0)
        return;
    selectedIndex++;
    if (selectedIndex >= package->NumberOfFiles())
        selectedIndex = 0;
    setView();
}

void PreviewDialog::Prev() {
    if (package == 0)
        return;
    selectedIndex--;
    if (selectedIndex < 0)
        selectedIndex = package->NumberOfFiles() - 1;
    setView();
}

void PreviewDialog::CloseDialog() {
    this->close();
}

void PreviewDialog::setView() {
    if (audio->isValid()) {
        widget.progressBar->setSliderPosition(0);
        audio->stop();
    }
    QString title = QString("Preview");
    this->setWindowTitle(title);
    this->widget.prevWidget->setCurrentWidget(this->widget.UnknownPage);

    if (package == 0 || package->NumberOfFiles() == 0) {
        return;
    }
    FileHandler* file = (*package)[selectedIndex];
    if (file == 0) {
        return;
    }
    title += QString(" - ") + QString(file->getFileName().c_str()) + QString("");
    this->setWindowTitle(title);
    QFontDatabase db;
    QFont font;
    switch (file->getDataType()) {
        case FileHandler::DATA_TYPE_AUDIO:
            this->audio->setCurrentSource(
                    Phonon::MediaSource(file->getFilePath().c_str()));
            this->widget.prevWidget->setCurrentWidget(this->widget.AudioPage);
            audio->play();
            return;
        case FileHandler::DATA_TYPE_FONT:

            if (this->fontId != -1) {
                db.removeApplicationFont(fontId);
            }
            fontId = db.addApplicationFont(file->getFilePath().c_str());
            font = QFont(*(db.applicationFontFamilies(fontId).begin()), 12);
            this->widget.label->setFont(font);
            this->widget.prevWidget->setCurrentWidget(this->widget.FontPage);
            return;
        case FileHandler::DATA_TYPE_IMAGE:
            if (img != 0) {
                delete img;
            }
            img = new QPixmap((*package)[selectedIndex]->getFilePath().c_str());
            this->widget.prevWidget->setCurrentWidget(this->widget.imagePage);
            resetImageView();
            return;
    }
}

void PreviewDialog::closeEvent(QCloseEvent*) {
    audio->stop();
}

void PreviewDialog::resetImageView() {
    if (package == 0 || package->NumberOfFiles() == 0)
        return;
    if ((*package)[selectedIndex]->getDataType() != FileHandler::DATA_TYPE_IMAGE)
        return;
    QPixmap pixmap;
    if (img->width() > widget.imgPrev->width() || img->height() > widget.imgPrev->height()) {
        pixmap = img->scaled(widget.imgPrev->width(), widget.imgPrev->height(), Qt::KeepAspectRatio,
                Qt::SmoothTransformation);
    } else {
        pixmap = *img;
    }
    widget.imgPrev->setPixmap(pixmap);
}

void PreviewDialog::SetTicks(qint64 pos) {
    widget.progressBar->setValue(pos);
    setProgDisplay(pos);
}

void PreviewDialog::setProgDisplay(int passed) {
    //qint64 passed = audio->currentTime();
    qint64 total = audio->totalTime();

    qint32 p_m = passed / 60000;
    qint32 p_s = (passed % 60000) / 1000;

    qint32 t_m = total / 60000;
    qint32 t_s = (total % 60000) / 1000;

    QString text = getTimeNumber(p_m) + QString(":") +
            getTimeNumber(p_s) + QString(" / ") +
            getTimeNumber(t_m) + QString(":") +
            getTimeNumber(t_s);
    widget.progDisplay->setText(text);
}

QString PreviewDialog::getTimeNumber(qint32 number) {
    if (number < 10)
        return QString("0") + QVariant(number).toString();
    else
        return QVariant(number).toString();
}

void PreviewDialog::SetAudioPosition(int) {
    audio->seek(widget.progressBar->value());
    setProgDisplay(widget.progressBar->value());
}

void PreviewDialog::stateChanged(Phonon::State newstate) {
    if (newstate == Phonon::PlayingState) {
        widget.progressBar->setMaximum(audio->totalTime());

    }

}