function appwebview(item) {
  const newElement = document.createElement('ul');
  newElement.innerHTML = item;
  return newElement;
}

export default appwebview;